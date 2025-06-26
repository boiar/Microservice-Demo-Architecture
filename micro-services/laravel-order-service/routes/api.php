<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;




Route::middleware('auth.jwt')->group(function () {

    Route::get('cart', [CartController::class, 'getCartItems']);
    Route::post('cart/add', [CartController::class, 'addToCart']);
    Route::delete('cart/delete/{itemId}', [CartController::class, 'removeFromCart']);


    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
});






