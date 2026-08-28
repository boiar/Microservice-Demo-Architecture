<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{


    protected static function getSecret(): string
    {
        return env('JWT_SECRET', 'testing_secret_key');
    }

    protected static function getTTL(): int
    {
        return env('JWT_TTL', 3600); // Default 1 hour
    }

    protected static function getRefreshTTL(): int
    {
        return env('JWT_REFRESH_TTL', 604800); // Default 7 days
    }

    public static function generateToken($user): string
    {
        $payload = [
            'iss' => "app-jwt-token",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + self::getTTL(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        return self::encodeToken($payload);
    }

    public static function generateRefreshToken($userId): string
    {
        $payload = [
            'iss' => "app-jwt-refresh",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + self::getRefreshTTL(),
        ];

        return self::encodeToken($payload);
    }

    public static function decodeToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return json_decode(json_encode($decoded), true);
        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    public static function encodeToken(array $payload): string
    {
        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function getUserIdFromToken(): int
    {
        $user = auth()->user();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        return $user->id;
    }

    public static function getUserFromToken(): ?array
    {
        $token = request()->bearerToken();
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return (array) ($decoded->user ?? null);
        } catch (\Exception $e) {
            return null;
        }
    }
}
