<?php

namespace Tests\Unit\Stubs;

use App\Contracts\Repositories\IOrderRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use stdClass;

class OrderRepositoryStub implements IOrderRepository
{
    private static array $orders = [];
    private static int $nextId = 1;

    public function __construct()
    {
        if (empty(self::$orders)) {
            self::$orders = [
                1 => (object)[
                    'id' => 1,
                    'user_id' => 1,
                    'status' => 'pending',
                    'total_price' => 100.00,
                    'address' => 'Test Address',
                    'note' => 'Test Note',
                    'created_at' => now(),
                ],
            ];
        }
    }

    public function create(array $data): mixed
    {
        $data['id'] = self::$nextId++;
        $data['created_at'] = now();
        $order = (object)$data;
        self::$orders[$order->id] = $order;
        return $order;
    }

    public function update(int|string $id, array $data): mixed
    {
        if (!isset(self::$orders[$id])) {
            return null;
        }

        foreach ($data as $key => $value) {
            self::$orders[$id]->$key = $value;
        }

        return self::$orders[$id];
    }

    public function delete(int|string $id): bool
    {
        if (!isset(self::$orders[$id])) {
            return false;
        }

        unset(self::$orders[$id]);
        return true;
    }

    public function getUserOrders(int|string $userId): Collection
    {
        $orders = array_filter(self::$orders, fn($order) => $order->user_id == $userId);
        return collect(array_values($orders));
    }

    public function getOrderById(int|string $orderId): mixed
    {
        return self::$orders[$orderId] ?? null;
    }

    public function userOwnsOrder(int $userId, int $orderId): bool
    {
        $order = self::$orders[$orderId] ?? null;
        return $order && $order->user_id == $userId;
    }
}
