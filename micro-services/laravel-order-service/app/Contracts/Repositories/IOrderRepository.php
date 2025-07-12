<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;

interface IOrderRepository
{



    public function create(array $data): mixed;

    public function update(int|string $id, array $data): mixed;

    public function delete(int|string $id): bool;

    public function getUserOrders(int|string $userId): Collection;

    public function getOrderById(int|string $orderId): mixed;

    public function userOwnsOrder(int $userId, int $orderId): bool;
}
