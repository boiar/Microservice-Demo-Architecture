<?php

namespace App\Models;

use App\Helpers\JwtHelper;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',

    ];


    public static function getCartItems()
    {
        $userId = JwtHelper::getUserIdFromToken();

        return self::select(
            'carts.id as cart_id',
            'carts.quantity',
            'products.id as product_id',
            'products.name as product_name',
            'products.price',
        )->join('products', 'carts.product_id', '=', 'products.id')
         ->where('carts.user_id', $userId)
         ->get();

    }
}
