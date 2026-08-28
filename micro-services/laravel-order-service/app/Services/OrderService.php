<?php


namespace App\Services;

use App\Contracts\Repositories\ICartRepository;
use App\Contracts\Repositories\IOrderItemRepository;
use App\Contracts\Repositories\IOrderRepository;
use App\Contracts\Services\IJwtService;
use App\Contracts\Services\IOrderService;
use App\Contracts\Services\IRabbitMQService;
use App\DTOs\CreateOrderDTO;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;

class OrderService implements IOrderService
{
    protected IOrderRepository $orderRepo;
    protected IOrderItemRepository $orderItemRepo;
    protected ICartRepository $cartRepo;
    protected IJwtService $jwtService;
    protected IRabbitMQService $rabbitMQService;


    public function __construct(
        IOrderRepository $orderRepo,
        IOrderItemRepository $orderItemRepo,
        ICartRepository $cartRepo,
        IJwtService $jwtService,
        IRabbitMQService $rabbitMQService
    ) {
        $this->orderRepo      = $orderRepo;
        $this->orderItemRepo  = $orderItemRepo;
        $this->cartRepo       = $cartRepo;
        $this->jwtService    = $jwtService;
        $this->rabbitMQService = $rabbitMQService;
    }



    public function getUserOrders(): object
    {
        $userId = $this->jwtService->getUserIdFromToken();
        $orders = $this->orderRepo->getUserOrders($userId);
        return ResponseHelper::returnData($orders);
    }


    public function getOrderDetails(int $orderId): object
    {
        $userId = $this->jwtService->getUserIdFromToken();

        if (!$this->orderRepo->userOwnsOrder($userId, $orderId)) {
            return ResponseHelper::returnError(403, 'This action is unauthorized.');
        }

        $orderInfo  = $this->orderRepo->getOrderById($orderId);
        $orderItems = $this->orderItemRepo->getOrderItemsByOrderId($orderId);

        return ResponseHelper::returnData([
            'order' => $orderInfo,
            'items' => $orderItems
        ]);

    }

    public function createOrder(CreateOrderDTO $dto): ?object
    {
        $user = $this->jwtService->getUserFromToken();
        $userId = $user['id'];

        $cartItems = $this->cartRepo->getUserCartItems($userId);

        if ($cartItems->isEmpty()) {
            return ResponseHelper::returnError(400, "Cart is empty");
        }

        return DB::transaction(function () use ($dto, $user, $cartItems, $userId) {
            $order = $this->orderRepo->create([
                  'user_id'     => $userId,
                  'address'     => $dto->getAddress(),
                  'note'        => $dto->getNotes(),
                  'status'      => 'pending',
                  'total_price' => 0,
            ]);

            $totalPrice = 0;
            $data       = [];

            foreach ($cartItems as $item) {
                $lineTotal = $item->price * $item->quantity;
                $totalPrice += $lineTotal;

                $data[] = [
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $this->orderItemRepo->insert($data);
            $this->orderRepo->update($order->id, ['total_price' => $totalPrice]);

            $this->rabbitMQService->publish('example_exchange', 'example_routing_key', [
               'event'      => 'order.created',
               'user_id'    => $userId,
               'user_email' => $user['email'] ?? null,
               'order_id'   => $order->id,
               'products'   => collect($data)->map(fn($item) => [
                   'product_id' => $item['product_id'],
                   'qty'        => $item['quantity'],
               ])->toArray(),
            ]);

            $this->cartRepo->clearUserCart($userId);

            return ResponseHelper::returnData([
                'order' => $order,
                'items' => $data
            ]);
        });
    }
}
