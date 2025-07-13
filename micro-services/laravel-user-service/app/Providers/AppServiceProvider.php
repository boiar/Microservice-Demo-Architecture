<?php

namespace App\Providers;

use App\Contracts\Repositories\IUserRepository;
use App\Contracts\Services\IAuthService;
use App\Contracts\Services\IJwtService;
use App\Contracts\Services\IUserService;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\JwtService;
use App\Services\UserService;
use App\tests\Unit\Stubs\UserRepositoryStub;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
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
