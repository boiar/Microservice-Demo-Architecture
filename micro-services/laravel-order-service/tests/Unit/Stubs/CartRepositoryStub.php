<?php

namespace Tests\Unit\Stubs;

use App\Contracts\Repositories\ICartRepository;
use App\Models\Cart;
use Illuminate\Support\Collection;

class CartRepositoryStub implements ICartRepository
{
    private static array $cartItems = [];
    private static int $nextId = 1;

    public function __construct()
    {
        if (empty(self::$cartItems)) {
            self::$cartItems = [
                1 => new Cart([
                    'id' => 1,
                    'user_id' => 1,
                    'product_id' => 1,
                    'quantity' => 2,
                ]),
            ];
        }
    }

    public function getUserCartItems(int $userId): Collection
    {
        return collect(array_filter(self::$cartItems, fn($item) => $item->user_id === $userId));
    }

    public function findUserCartItem(int $userId, int $productId): ?Cart
    {
        foreach (self::$cartItems as $item) {
            if ($item->user_id === $userId && $item->product_id === $productId) {
                return $item;
            }
        }
        return null;
    }

    public function create(array $data): Cart
    {
        $data['id'] = self::$nextId++;
        $cart = new Cart($data);
        self::$cartItems[$cart->id] = $cart;
        return $cart;
    }

    public function updateQuantity(Cart $cartItem, int $quantity): bool
    {
        $cartItem->quantity = $quantity;
        self::$cartItems[$cartItem->id] = $cartItem;
        return true;
    }

    public function deleteItem(int $userId, int $itemId): bool
    {
        if (isset(self::$cartItems[$itemId]) && self::$cartItems[$itemId]->user_id === $userId) {
            unset(self::$cartItems[$itemId]);
            return true;
        }
        return false;
    }

    public function clearUserCart(int $userId): bool
    {
        self::$cartItems = array_filter(self::$cartItems, fn($item) => $item->user_id !== $userId);
        return true;
    }

    public function getCartTotal(int $userId): float
    {
        return $this->getUserCartItems($userId)->sum(function ($item) {
            return 10 * $item->quantity; // assume each product price = 10 for testing
        });
    }
}
