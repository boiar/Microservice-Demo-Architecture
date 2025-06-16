<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
   try {
       Redis::set('test_key', 'Hello Redis');

       $value = Redis::get('test_key');

       return response()->json([
           'message' => 'Redis test successful',
           'redis_value' => $value,
       ], 200);

   } catch (\Exception $e) {
       \Illuminate\Support\Facades\Log::error('Redis connection failed: ' . $e->getMessage());
       return response()->json([
           'message' => 'Redis connection failed',
           'error' => $e->getMessage(),
       ], 500);
   }
});




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth.jwt')->post('/user/profile', [UserController::class, 'updateProfile']);

Route::post('/auth/refresh', [AuthController::class, 'refreshToken']);
