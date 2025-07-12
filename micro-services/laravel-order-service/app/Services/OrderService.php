<?php


namespace App\Services;

use App\Contracts\Repositories\ICartRepository;
use App\Contracts\Repositories\IOrderItemRepository;
use App\Contracts\Repositories\IOrderRepository;
use App\Contracts\Repositories\IProductRepository;
use App\Contracts\Services\IJwtService;
use App\Contracts\Services\IOrderService;
use App\DTOs\CreateOrderDTO;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderService implements IOrderService
{
    protected IOrderRepository $orderRepo;
    protected IOrderItemRepository $orderItemRepo;
    protected ICartRepository $cartRepo;
    protected IProductRepository $productRepo;
    protected IJwtService $jwtService;


    public function __construct(
        IOrderRepository $orderRepo,
        IOrderItemRepository $orderItemRepo,
        ICartRepository $cartRepo,
        IProductRepository $productRepo,
        IJwtService $jwtService

    ) {
        $this->orderRepo      = $orderRepo;
        $this->orderItemRepo  = $orderItemRepo;
        $this->cartRepo       = $cartRepo;
        $this->productRepo    = $productRepo;
        $this->jwtService    = $jwtService;

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

            $productIds = $cartItems->pluck('product_id')->toArray();
            $products   = $this->productRepo->findByIdsWithLock($productIds);
            $totalPrice = 0;
            $data       = [];

            foreach ($cartItems as $item) {
                $product = $products->get($item->product_id);

                if (!$product) {
                    throw new \Exception("Product not found.");
                }

                if ($product->qty < $item->quantity) {
                    throw new \Exception("Insufficient quantity for product: {$product->name}");
                }

                $product->qty -= $item->quantity;
                $this->productRepo->update($product->id, ['qty' => $product->qty]);

                $lineTotal = $product->price * $item->quantity;
                $totalPrice += $lineTotal;

                $data[] = [
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $this->orderItemRepo->insert($data);
            $this->orderRepo->update($order->id, ['total_price' => $totalPrice]);

            Redis::publish('order-events', json_encode([
               'event'      => 'order.created',
               'user_id'    => $userId,
               'user_email' => $user['email'] ?? null,
               'order_id'   => $order->id,
               'products'   => collect($data)->map(fn($item) => [
                   'product_id' => $item['product_id'],
                   'qty'        => $item['quantity'],
               ])->toArray(),
            ]));

            $this->cartRepo->clearUserCart($userId);

            return ResponseHelper::returnData([
                'order' => $order,
                'items' => $data
            ]);
        });
    }
}
