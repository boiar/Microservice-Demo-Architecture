<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddItemsToCartRequest;
use App\Services\CartService;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }


    public function getCartItems()
    {
        return $this->cartService->getCartItems();

    }


    public function addToCart(AddItemsToCartRequest $request) : ?object
    {
        return $this->cartService->addToCart($request->getDto());
    }

    public function removeFromCart($itemId) : ?object
    {
        return $this->cartService->removeFromCart($itemId);
    }
}
