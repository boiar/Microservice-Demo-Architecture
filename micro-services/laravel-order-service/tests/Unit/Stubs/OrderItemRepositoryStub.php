<?php

namespace Tests\Unit\Stubs;

use App\Contracts\Repositories\IOrderItemRepository;
use Illuminate\Support\Collection;
use stdClass;

class OrderItemRepositoryStub implements IOrderItemRepository
{
    private static array $orderItems = [];

    public function __construct()
    {
        if (empty(self::$orderItems)) {
            self::$orderItems = [
                1 => [
                    (object)[
                        'order_id' => 1,
                        'product_id' => 100,
                        'quantity' => 2,
                        'price' => 50.00,
                        'product_name' => 'Sample Product',
                    ],
                ]
            ];
        }
    }

    public function getOrderItemsByOrderId(int|string $orderId): Collection
    {
        return collect(self::$orderItems[$orderId] ?? []);
    }

    public function insert(array $data): bool
    {
        foreach ($data as $item) {
            $orderId = $item['order_id'];
            self::$orderItems[$orderId][] = (object)$item;
        }
        return true;
    }
}
