<?php

namespace App\Contracts;
use App\DTOs\AddItemToCartDTO;
use App\DTOs\CreateOrderDTO;

interface IOrder
{
    public function getUserOrders(): object;

    public function getOrderDetails(int $orderId): object;

    public function createOrder(CreateOrderDTO $dto): ?object;


}
