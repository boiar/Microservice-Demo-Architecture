<?php

namespace App\Contracts\Services;
use App\DTOs\CreateOrderDTO;

interface IOrderService
{
    public function getUserOrders(): object;

    public function getOrderDetails(int $orderId): object;

    public function createOrder(CreateOrderDTO $dto): ?object;


}
