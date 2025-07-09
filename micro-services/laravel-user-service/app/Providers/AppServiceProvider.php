<?php

namespace App\Providers;

use App\Contracts\Repositories\IUserRepository;
use App\Contracts\Services\IAuth;
use App\Contracts\Services\IUser;
use App\Repositories\UserRepository;
use App\Services\AuthService;
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
        $this->app->bind(IAuth::class, AuthService::class);
        $this->app->bind(IUser::class, UserService::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
