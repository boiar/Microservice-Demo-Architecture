<?php

namespace App\Http\Middleware;

use app\Helpers\JwtHelper;
use app\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if (!$token) {
            return ResponseHelper::returnError(401, 'Token not provided');
        }

        try {
            $decoded = JwtHelper::decodeToken($token);

            if (isset($decoded['error'])) {
                return ResponseHelper::returnError(401, 'Invalid or expired token');
            }

            $request->merge(['user_id' => $decoded['sub']]);


            Auth::loginUsingId($decoded['sub']);

        } catch (\Exception $e) {
            return ResponseHelper::returnError(401, 'Invalid or expired token');
        }


        return $next($request);
    }
}
