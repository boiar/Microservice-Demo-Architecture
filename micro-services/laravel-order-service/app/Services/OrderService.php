<?php


namespace App\Services;

use App\Contracts\IOrder;
use App\DTOs\CreateOrderDTO;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderService implements IOrder
{

    public function getUserOrders(): object
    {
        $orders = collect(Order::getUserOrders())->toArray();
        return ResponseHelper::returnData($orders);
    }


    public function getOrderDetails(int $orderId): object
    {
        $userId = JwtHelper::getUserIdFromToken();

        $orderExists = DB::table('orders')
                         ->where('id', $orderId)
                         ->where('user_id', $userId)
                         ->exists();

        if (!$orderExists) {
            return ResponseHelper::returnError(403, 'This action is unauthorized.');
        }

        $orderInfo  = Order::orderById($orderId);
        $orderItems = OrderItem::orderItemsByOrderId($orderId);

        return ResponseHelper::returnData([
          'order' => $orderInfo,
          'items' => $orderItems
        ]);

    }

    public function createOrder(CreateOrderDTO $dto): ?object
    {
        $user = JwtHelper::getUserFromToken();


        $cartItems = Cart::where('user_id', $user['id'])->get();

        if ($cartItems->isEmpty()) {
             return ResponseHelper::returnError(400, "Cart is empty");
        }

        return DB::transaction(function () use ($dto, $user, $cartItems) {

            $order = Order::create([
               'user_id' => $user['id'],
               'address' => $dto->getAddress(),
               'note'    => $dto->getNotes(),
               'status'  => Order::STATUS_PENDING,
               'total_price' => 0,
            ]);

            $productIds = $cartItems->pluck('product_id')->toArray();
            $products   = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
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

                // Decrease product quantity
                $product->qty -= $item->quantity;
                $product->save();

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

            OrderItem::insert($data);
            $order->update(['total_price' => $totalPrice]);

            Redis::publish('order-events', json_encode([
                'event' => 'order.created',
                'user_id' => $user['id'] ?? null,
                'user_email' => $user['email'] ?? null,
                'order_id' => $order->id,
                'products' => collect($data)->map(fn($item) => [
                    'product_id' => $item['product_id'],
                    'qty' => $item['quantity'],
                ])->toArray(),
            ]));

            Cart::where('user_id', $user['id'])->delete();

            return ResponseHelper::returnData([
              'order' => $order->fresh(),
              'items' => $data
            ]);
        });
    }
}
