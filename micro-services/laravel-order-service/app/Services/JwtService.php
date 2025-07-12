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
        $this->secret = config('jwt.secret', 'testing_secret_key');
        $this->ttl = config('jwt.ttl', 3600);
        $this->refreshTtl = config('jwt.refresh_ttl', 604800);
    }

    public function generateToken($user): string
    {
        $payload = [
            'iss' => "app-jwt-token",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + $this->ttl,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function decodeToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return json_decode(json_encode($decoded), true);
        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function generateRefreshToken($userId): string
    {
        $payload = [
            'iss' => "app-jwt-refresh",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $this->refreshTtl,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }


    public function getUserIdFromToken(): int
    {
        $user = auth()->user();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        return $user->id;
    }

    public function getUserFromToken(): ?array
    {
        $token = request()->bearerToken();
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) ($decoded->user ?? null);
        } catch (\Exception $e) {
            return null;
        }
    }

}
