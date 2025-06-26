<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $fillable = ['product_id', 'quantity', 'price'];


    public static function orderItemsByOrderId($orderId): ?object
    {
        return self::select(
                    'order_items.product_id',
                    'order_items.quantity',
                    'order_items.price as item_price',
                    'products.name as product_name',

                )->join('products', 'order_items.product_id', '=', 'products.id')
                 ->where('order_items.order_id', $orderId)
                 ->get();

    }
}
