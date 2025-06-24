<?php


namespace App\DTOs;

class CreateOrderDTO
{
    private string $address;
    private ?string $notes = null;

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}
