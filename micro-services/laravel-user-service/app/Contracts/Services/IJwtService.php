<?php


namespace App\Contracts\Services;

interface IJwtService
{
    public function generateToken(object $user): string;

    public function generateRefreshToken(int $userId): string;

    public function decodeToken(string $token): array;

    public function encodeToken(array $payload): string;
}
