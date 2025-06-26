<?php

namespace App\Providers;

use App\Contracts\ICart;
use App\Contracts\IOrder;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ICart::class, CartService::class);
        $this->app->bind(IOrder::class, OrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
