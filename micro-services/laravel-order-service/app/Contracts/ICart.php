<?php

namespace App\Contracts;
use App\DTOs\AddItemToCartDTO;

interface ICart
{
    public function getCartItems(): object;

    public function addToCart(AddItemToCartDTO $dto): ?object;

    public function removeFromCart(int $itemId): ?object;


}
