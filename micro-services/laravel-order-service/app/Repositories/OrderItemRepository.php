<?php

namespace App\Repositories;

use App\Contracts\Repositories\IOrderItemRepository;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

class OrderItemRepository implements IOrderItemRepository
{

    public function getOrderItemsByOrderId(int|string $orderId): Collection
    {
        return OrderItem::select(
            'order_items.product_id',
            'order_items.quantity',
            'order_items.price as item_price',
            'products.name as product_name'

        )->join('products', 'order_items.product_id', '=', 'products.id')
         ->where('order_items.order_id', $orderId)
         ->get();
    }

    /**
     * Insert multiple order items.
     */
    public function insert(array $data): bool
    {
        return OrderItem::insert($data);
    }
}
