<?php

namespace App\Contracts\Repositories;

use App\Models\OrderItem;
use Illuminate\Support\Collection;

interface IOrderItemRepository
{
    public function getOrderItemsByOrderId(int|string $orderId): Collection;

    public function insert(array $data): bool;

}
