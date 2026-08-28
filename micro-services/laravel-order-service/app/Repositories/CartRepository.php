<?php

namespace App\Repositories;

use App\Contracts\Repositories\ICartRepository;
use App\Helpers\JwtHelper;
use App\Models\Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartRepository implements ICartRepository
{
    public function getUserCartItems(int $userId): Collection
    {
        return Cart::select(
            'id as cart_id',
            'product_id',
            'quantity',
            'product_name',
            'price',
        )->where('user_id', $userId)
         ->get();
    }

    public function findUserCartItem(int $userId, int $productId): ?Cart
    {
        return Cart::where('user_id', $userId)
                   ->where('product_id', $productId)
                   ->first();
    }

    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    public function updateQuantity(Cart $cartItem, int $quantity): bool
    {
        $cartItem->quantity = $quantity;
        return $cartItem->save();
    }

    public function deleteItem(int $userId, int $itemId): bool
    {
        return Cart::where('user_id', $userId)
                   ->where('id', $itemId)
                   ->delete();
    }

    public function clearUserCart(int $userId): bool
    {
        return Cart::where('user_id', $userId)->delete();
    }

    public function getCartTotal(int $userId): float
    {
        return (float) Cart::where('user_id', $userId)
                           ->selectRaw('SUM(quantity * price) as total')
                           ->value('total');
    }
}
