<?php

namespace App\Contracts\Services;
use App\DTOs\AddItemToCartDTO;

interface ICartService
{
    public function getCartItems(): object;

    public function addToCart(AddItemToCartDTO $dto): ?object;

    public function removeFromCart(int $itemId): ?object;


}
