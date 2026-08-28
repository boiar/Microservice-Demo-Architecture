<?php

namespace App\DTOs;

class AddItemToCartDTO
{
    private int $productId;
    private int $quantity;



    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

}
