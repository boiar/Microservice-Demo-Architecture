<?php


namespace App\Services;

use App\Contracts\Services\IJwtService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService implements IJwtService
{
    protected string $secret;
    protected int $ttl;
    protected int $refreshTtl;

    public function __construct()
    {
        $this->secret     = config('jwt.secret', env('JWT_SECRET', 'default_jwt_secret'));
        $this->ttl        = config('jwt.ttl', 7200);
        $this->refreshTtl = config('jwt.refresh_ttl', 604800);
    }

    public function generateToken(object $user): string
    {
        $payload = [
            'iss'  => "app-jwt-token",
            'sub'  => $user->id,
            'iat'  => time(),
            'exp'  => time() + $this->ttl,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ];

        return $this->encodeToken($payload);
    }

    public function generateRefreshToken(int $userId): string
    {
        $payload = [
            'iss' => "app-jwt-refresh",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $this->refreshTtl,
        ];

        return $this->encodeToken($payload);
    }

    public function decodeToken(string $token): array
    {
        try {
            return (array) JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (\Throwable $e) {
            return [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function encodeToken(array $payload): string
    {
        return JWT::encode($payload, $this->secret, 'HS256');
    }
}
