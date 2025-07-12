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

    protected IJwtService $jwtService;

    public function __construct(IJwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }


    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if (!$token) {
            return ResponseHelper::returnError(401, 'Token not provided');
        }

        try {
            $decoded = $this->jwtService->decodeToken($token);
            $request->merge(['user_id' => $decoded['sub']]);
            Auth::loginUsingId($decoded['sub']);

        } catch (\Exception $e) {
            return ResponseHelper::returnError(401, 'Invalid or expired token');
        }


        return $next($request);
    }
}
