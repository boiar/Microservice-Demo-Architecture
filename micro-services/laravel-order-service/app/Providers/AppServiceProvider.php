<?php

namespace App\Providers;

use App\Contracts\Repositories\ICartRepository;
use App\Contracts\Repositories\IOrderItemRepository;
use App\Contracts\Repositories\IOrderRepository;
use App\Contracts\Repositories\IProductRepository;
use App\Contracts\Services\ICartService;
use App\Contracts\Services\IJwtService;
use App\Contracts\Services\IOrderService;
use App\Repositories\CartRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\CartService;
use App\Services\JwtService;
use App\Services\OrderService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ICartService::class, CartService::class);
        $this->app->bind(IOrderService::class, OrderService::class);
        $this->app->bind(ICartRepository::class, CartRepository::class);
        $this->app->bind(IOrderRepository::class,OrderRepository::class);
        $this->app->bind(IProductRepository::class,ProductRepository::class);
        $this->app->bind(IOrderItemRepository::class,OrderItemRepository::class);
        $this->app->bind(IJwtService::class, JwtService::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
