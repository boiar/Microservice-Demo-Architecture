<?php

namespace Tests;

use App\Contracts\Services\IJwtService;
use App\Services\JwtService;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
           'cache.default' => 'array',
           'session.driver' => 'array',
           'queue.default' => 'sync',
           'database.redis.default.host' => '127.0.0.1',
           'database.redis.cache.host' => '127.0.0.1',
           'broadcast.connections.redis.host' => '127.0.0.1',
        ]);

        $this->app->bind(IJwtService::class, JwtService::class);

    }

    protected function createUserAndToken(): array
    {
        $user = \App\Models\User::factory()->create();

        /** @var IJwtService $jwtService */
        $jwtService = $this->app->make(IJwtService::class);

        $token = $jwtService->generateToken($user);

        return [$user, $token];
    }




}
