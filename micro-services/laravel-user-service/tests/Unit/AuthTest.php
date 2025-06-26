<?php

namespace Tests\Unit;
use App\DTOs\LoginUserDTO;
use Tests\TestCase;

use App\DTOs\RegisterUserDTO;
use Illuminate\Support\Str;
use Tests\Stubs\Services\AuthStubService;

class AuthTest extends TestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        $this->app->bind(\App\Services\AuthService::class, AuthStubService::class);
    }


    public function test_register_user(): void
    {
        $dto = new RegisterUserDTO();
        $dto->setName('Test User');
        $dto->setEmail('test@example.com');
        $dto->setPassword('password123');

        $authService = app(\App\Services\AuthService::class);

        $response = $authService->register($dto);

        $data = $response->getData(true);

        $this->assertArrayHasKey('user', $data['data']);
        $this->assertArrayHasKey('token', $data['data']);
        $this->assertArrayHasKey('refresh_token', $data['data']);
    }

    public function test_register_user_with_existing_email_should_fail(): void
    {
        $dto1 = new RegisterUserDTO();
        $dto1->setName('Test User');
        $dto1->setEmail('duplicate@example.com');
        $dto1->setPassword('password123');

        $dto2 = new RegisterUserDTO();
        $dto2->setName('Another User');
        $dto2->setEmail('duplicate@example.com');
        $dto2->setPassword('differentPassword');

        $authService = app(\App\Services\AuthService::class);

        // First registration
        $firstResponse = $authService->register($dto1);
        $firstData = $firstResponse->getData(true);
        $this->assertArrayHasKey('user', $firstData['data']);

        // Second registration
        $secondResponse = $authService->register($dto2);
        $secondData = $secondResponse->getData(true);

        $this->assertEquals(409, $secondData['code']);
    }




    public function test_login_successful()
    {
        $registerDto = new RegisterUserDTO();
        $registerDto->setName('Test User');
        $registerDto->setEmail('login@example.com');
        $registerDto->setPassword('password123');

        app(\App\Services\AuthService::class)->register($registerDto);


        $loginDto = new LoginUserDTO();
        $loginDto->setEmail('login@example.com');
        $loginDto->setPassword('password123');

        $response = app(\App\Services\AuthService::class)->login($loginDto);

        $data = $response->getData(true);

        $this->assertEquals('Login successful', $data['msg']);
        $this->assertArrayHasKey('token', $data['data']);
    }



    public function test_login_with_invalid_email()
    {
        $registerDto = new RegisterUserDTO();
        $registerDto->setName('Test User');
        $registerDto->setEmail('register@example.com');
        $registerDto->setPassword('password123');

        app(\App\Services\AuthService::class)->register($registerDto);


        $loginDto = new LoginUserDTO();
        $loginDto->setEmail('invalid_email@example.com');
        $loginDto->setPassword('password123');

        $response = app(\App\Services\AuthService::class)->login($loginDto);

        $data = $response->getData(true);

        $this->assertEquals('Invalid Credentials', $data['msg']);
        $this->assertEquals(401, $data['code']);
    }


    public function test_login_with_invalid_password()
    {
        $registerDto = new RegisterUserDTO();
        $registerDto->setName('Test User');
        $registerDto->setEmail('login@example.com');
        $registerDto->setPassword('password123');

        app(\App\Services\AuthService::class)->register($registerDto);


        $loginDto = new LoginUserDTO();
        $loginDto->setEmail('invalid_email@example.com');
        $loginDto->setPassword('password');

        $response = app(\App\Services\AuthService::class)->login($loginDto);

        $data = $response->getData(true);

        $this->assertEquals('Invalid Credentials', $data['msg']);
        $this->assertEquals(401, $data['code']);
    }
}
