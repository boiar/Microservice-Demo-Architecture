<?php

namespace App\Services;

use App\DTOs\AddItemToCartDTO;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\Product;
use App\Contracts\ICart;


class CartService implements ICart
{

    public function getCartItems(): object
    {
        $cartItems = collect(Cart::getCartItems())->toArray();
        return ResponseHelper::returnData($cartItems);
    }


    public function addToCart(AddItemToCartDTO $dto): ?object
    {
        $product = Product::where('id', $dto->getProductId())->first();
        if(!is_object($product)){
            return ResponseHelper::returnError(404, 'Invalid Product');
        }


        $userId = JwtHelper::getUserIdFromToken();

        // Check product exists in cart
        $existingItem = Cart::where('user_id', $userId)
                            ->where('product_id', $dto->getProductId())
                            ->first();


        $requestedQty = $dto->getQuantity();
        // Calculate total quantity
        $currentQtyInCart = $existingItem ? $existingItem->quantity : 0;
        $totalQty = $currentQtyInCart + $requestedQty;

        // Check against product available quantity
        if ($totalQty > $product->available_quantity) {
            return ResponseHelper::returnError(400, 'Quantity exceeds available stock.');
        }



        if ($existingItem) {
            // Update qty
            $existingItem->quantity += $dto->getQuantity();
            $existingItem->save();

            return $existingItem;
        }

        // Insert into cart
        $cartItem = Cart::create([
             'user_id' => $userId,
             'product_id' => $dto->getProductId(),
             'quantity' => $dto->getQuantity(),
        ]);

        return $cartItem;
    }


    public function removeFromCart(int $itemId): ?object
    {

        $userId = JwtHelper::getUserIdFromToken();

        $cartItem = Cart::where('user_id', $userId)
                        ->where('id', $itemId)
                        ->first();

        if (!$cartItem) {
            return ResponseHelper::returnError(404, 'Item not found in cart');
        }

        $cartItem->delete();

        return ResponseHelper::returnSuccessMessage('Item removed from cart successfully', 200);
    }
}
