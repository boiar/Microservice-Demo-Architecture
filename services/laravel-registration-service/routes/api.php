<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Redis;


Route::get('/', function () {
   try {
       // Set a test key
       Redis::set('test_key', 'Hello Redis');

       // Retrieve the test key
       $value = Redis::get('test_key');

       return response()->json([
           'message' => 'Redis test successful',
           'redis_value' => $value,
       ], 200);

   } catch (\Exception $e) {
       Log::error('Redis connection failed: ' . $e->getMessage());
       return response()->json([
           'message' => 'Redis connection failed',
           'error' => $e->getMessage(),
       ], 500);
   }
});


Route::post('/register', [AuthController::class, 'register']);
