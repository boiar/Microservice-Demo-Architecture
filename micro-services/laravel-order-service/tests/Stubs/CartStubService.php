<?php

namespace Tests\Stubs;

use App\Contracts\ICart;
use App\DTOs\AddItemToCartDTO;

class CartStubService implements ICart
{
    private array $products = [
        10 => ['name' => 'Laptop', 'description' => 'Gaming laptop', 'price' => 2500.00, 'qty' => 10],
        20 => ['name' => 'Mouse', 'description' => 'Wireless mouse', 'price' => 50.00, 'qty' => 100],
        30 => ['name' => 'Keyboard', 'description' => 'Mechanical keyboard', 'price' => 120.00, 'qty' => 50],
    ];

    private array $cart = [];

    public function getCartItems(): object
    {
        $items = [];

        foreach ($this->cart as $id => $item) {
            $product = $this->products[$item['product_id']] ?? null;

            if ($product) {
                $items[] = [
                    'id' => $id,
                    'user_id' => $item['user_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'product' => $product,
                ];
            }
        }

        return (object)[
            'code' => 200,
            'data' => $items
        ];
    }

    public function addToCart(AddItemToCartDTO $dto): ?object
    {
        $productId = $dto->getProductId();

        if (!isset($this->products[$productId])) {
            return (object)[
                'code' => 404,
                'message' => 'Invalid Product'
            ];
        }

        $requestedQty = $dto->getQuantity();
        $availableQty = $this->products[$productId]['qty'];

        // Check if already in cart and update
        foreach ($this->cart as $id => &$item) {
            if ($item['product_id'] === $productId) {
                $newQty = $item['qty'] + $requestedQty;

                if ($newQty > $availableQty) {
                    return (object)[
                        'code' => 400,
                        'message' => 'Quantity exceeds available stock.'
                    ];
                }

                $item['qty'] = $newQty;
                return (object)$item;
            }
        }

        // If not in cart, check availability for new entry
        if ($requestedQty > $availableQty) {
            return (object)[
                'code' => 400,
                'message' => 'Quantity exceeds available stock.'
            ];
        }

        $id = count($this->cart) + 1;

        $this->cart[$id] = [
            'id' => $id,
            'user_id' => 1, // Fake user ID for stub
            'product_id' => $productId,
            'quantity' => $requestedQty,
        ];

        return (object)$this->cart[$id];
    }


    public function removeFromCart(int $itemId): ?object
    {
        if (!isset($this->cart[$itemId])) {
            return (object)[
                'code' => 404,
                'message' => 'Item not found in cart'
            ];
        }

        unset($this->cart[$itemId]);

        return (object)[
            'code' => 200,
            'message' => "Item removed from cart successfully"
        ];
    }
}
