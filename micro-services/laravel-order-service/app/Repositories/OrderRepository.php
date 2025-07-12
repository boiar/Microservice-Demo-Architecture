<?php

namespace App\Repositories;

use App\Contracts\Repositories\IOrderRepository;
use App\Helpers\JwtHelper;
use App\Models\Order;
use Illuminate\Support\Collection;

class OrderRepository implements IOrderRepository
{
    public function getAll(): Collection
    {
        return Order::all();
    }

    public function getBy(string $column, string $operator, mixed $value): Collection
    {
        return Order::where($column, $operator, $value)->get();
    }

    public function findById(int|string $id): ?Order
    {
        return Order::find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(int|string $id, array $data): Order
    {
        $order = Order::findOrFail($id);
        $order->update($data);
        return $order;
    }

    public function delete(int|string $id): bool
    {
        $order = Order::find($id);
        if ($order) {
            return $order->delete();
        }
        return false;
    }

    public function getUserOrders($userId): Collection
    {
        return Order::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function getOrderById(int|string $orderId): ?object
    {
        return Order::select('id as order_id', 'address', 'total_price', 'status', 'created_at')
                    ->where('id', $orderId)
                    ->first();
    }

    public function userOwnsOrder(int $userId, int $orderId): bool
    {
        return Order::where('id', $orderId)
                    ->where('user_id', $userId)
                    ->exists();
    }
}
