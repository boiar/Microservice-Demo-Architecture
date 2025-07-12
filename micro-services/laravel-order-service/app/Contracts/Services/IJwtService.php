<?php

namespace App\Contracts\Services;

interface IJwtService
{
    public function generateToken($user): string;

    public function decodeToken(string $token): array;

    public function generateRefreshToken($userId): string;

    public function getUserIdFromToken(): int;

    public function getUserFromToken(): ?array;
}
