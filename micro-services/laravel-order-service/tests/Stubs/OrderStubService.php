<?php

namespace Tests\Stubs;

use App\Contracts\IOrder;
use App\DTOs\CreateOrderDTO;

class OrderStubService implements IOrder
{

    private array $orders = [];
    private array $orderItems = [];



    public function getUserOrders(): object
    {
        return (object) [
            'status' => true,
            'data' => $this->orders,
        ];
    }

    public function getOrderDetails(int $orderId): object
    {
        if (!isset($this->orders[$orderId])) {
            return (object) ['status' => false, 'message' => 'Order not found'];
        }

        return (object) [
            'status' => true,
            'data' => [
                'order' => $this->orders[$orderId],
                'items' => $this->orderItems[$orderId] ?? [],
            ]
        ];
    }

    public function createOrder(CreateOrderDTO $dto): ?object
    {
        $orderId = count($this->orders) + 1;

        $order = [
            'id' => $orderId,
            'user_id' => 1,
            'address' => $dto->getAddress(),
            'note' => $dto->getNotes(),
            'status' => 'pending',
            'total_price' => 200
        ];

        $items = [
            [
                'product_id' => 10,
                'quantity' => 2,
                'price' => 100
            ]
        ];

        $this->orders[$orderId] = $order;
        $this->orderItems[$orderId] = $items;

        return (object) [
            'status' => true,
            'data' => [
                'order' => $order,
                'items' => $items
            ]
        ];

    }
}
