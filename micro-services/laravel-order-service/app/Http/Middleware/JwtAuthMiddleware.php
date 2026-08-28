<?php

namespace App\Http\Middleware;

use App\Contracts\Services\IJwtService;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    public function __construct(protected IJwtService $jwtService) {}


    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ResponseHelper::returnError(401, 'Token not provided');
        }

        $decoded = $this->jwtService->decodeToken($token);

        if (isset($decoded['error'])) {
            return ResponseHelper::returnError(401, $decoded['message']);
        }

        // Optionally verify user exists in DB if needed
        $userId = $decoded['sub'] ?? null;
        if (!$userId) {
            return ResponseHelper::returnError(401, 'Invalid token payload');
        }

        $request->merge([
            'auth_user' => $decoded['user'] ?? null,
            'user_id' => $userId,
        ]);

        Auth::loginUsingId($userId);

        return $next($request);
    }
}
