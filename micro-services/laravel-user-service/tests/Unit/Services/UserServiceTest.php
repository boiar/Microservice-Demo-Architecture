<?php

namespace Tests\Unit\Services;

use App\Contracts\Services\IJwtService;
use App\DTOs\RegisterUserDTO;
use App\DTOs\UpdateUserProfileDTO;
use App\Models\User;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected IJwtService $jwtService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->singleton(\App\Contracts\Repositories\IUserRepository::class, \Tests\Unit\Stubs\UserRepositoryStub::class);

        $this->authService = app(\App\Services\AuthService::class);
        $this->userService = app(\App\Services\UserService::class);
        $this->jwtService = $this->app->make(IJwtService::class);
    }


    public function test_update_profile_successfully(): void
    {
        // Register the user
        $registerDto = new RegisterUserDTO();
        $registerDto->setName('Test User');
        $registerDto->setEmail('test_' . time() . '@example.com');
        $registerDto->setPassword('password123');

        $registerResponse = $this->authService->register($registerDto);
        $registerData = $registerResponse->getData(true);

        $refreshToken = $registerData['data']['refresh_token'];

        $decodedPayload = $this->jwtService->decodeToken($refreshToken);
        $userId = $decodedPayload['sub']; // 'sub' is user ID

        $userData = $registerData['data']['user'];
        $user = new User([
             'id' => $userId,
             'name' => $userData['name'],
             'email' => $userData['email'],
        ]);

        // Authenticate
        $this->be($user);

        $updateDto = new UpdateUserProfileDTO();
        $updateDto->setName('New Name');
        $updateDto->setPassword('new_password');

        $response = $this->userService->updateProfile($updateDto);
        $data = $response->getData(true);

        $this->assertEquals('Profile updated successfully', $data['msg']);
        $this->assertEquals('New Name', $data['data']['user']['name']);
    }



}
