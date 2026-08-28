<?php

namespace App\DTOs;

class UpdateUserProfileDTO
{
    private string $name;
    private ?string $password = null;

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
