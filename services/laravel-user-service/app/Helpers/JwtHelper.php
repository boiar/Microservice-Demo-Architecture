<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class JwtHelper
{
    public static function generateToken($user): string
    {
        $payload = [
            'iss' => "app-jwt-token",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + config('jwt.ttl', 7200),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
        ];

        return self::encodeToken($payload); // assuming you meant encodeToken, not decodeToken
    }


    public static function generateRefreshToken($userId): string
    {
        $payload = [
            'iss' => "app-jwt-refresh",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + config('jwt.refresh_ttl', 604800), // 1 week
        ];

        return self::encodeToken($payload);
    }


    public static function decodeToken(string $token): array
    {
        try {
            return (array) JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }


    public static function encodeToken(array $payload): string
    {
        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }




}
