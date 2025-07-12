<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;
use App\Models\Cart;

interface ICartRepository
{
    public function getUserCartItems(int $userId): Collection;

    public function findUserCartItem(int $userId, int $productId): ?Cart;

    public function create(array $data): Cart;

    public function updateQuantity(Cart $cartItem, int $quantity): bool;

    public function deleteItem(int $userId, int $itemId): bool;

    public function clearUserCart(int $userId): bool;

    public function getCartTotal(int $userId): float;
}
