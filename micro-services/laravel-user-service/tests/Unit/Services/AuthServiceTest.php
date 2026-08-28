<?php

namespace Tests\Unit\Services;
use App\Contracts\Repositories\IUserRepository;
use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\Stubs\UserRepositoryStub;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->singleton(IUserRepository::class, UserRepositoryStub::class);
        $this->authService = app(AuthService::class);
    }


    public function test_register_user(): void
    {
        $dto = new RegisterUserDTO();
        $dto->setName('Test User');
        $dto->setEmail('test@example.com');
        $dto->setPassword('password123');


        $response = $this->authService->register($dto);

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

        $authService = app(AuthService::class);

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
        $authService = app(AuthService::class);
        $registerDto = new RegisterUserDTO();
        $registerDto->setName('Test User');
        $registerDto->setEmail('login@example.com');
        $registerDto->setPassword('password123');


        $authService->register($registerDto);


        $loginDto = new LoginUserDTO();
        $loginDto->setEmail('login@example.com');
        $loginDto->setPassword('password123');

        $response = $authService->login($loginDto);

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
        $authService = app(AuthService::class);


        $authService->register($registerDto);


        $loginDto = new LoginUserDTO();
        $loginDto->setEmail('invalid_email@example.com');
        $loginDto->setPassword('password123');

        $response = $authService->login($loginDto);

        $data = $response->getData(true);

        $this->assertEquals('Invalid email or password', $data['msg']);
        $this->assertEquals(401, $data['code']);
    }


    public function test_login_with_invalid_password()
    {
        $registerDto = new RegisterUserDTO();
        $registerDto->setName('Test User');
        $registerDto->setEmail('login@example.com');
        $registerDto->setPassword('password123');

        $authService = app(AuthService::class);


        $authService->register($registerDto);


        $loginDto = new LoginUserDTO();
        $loginDto->setEmail('invalid_email@example.com');
        $loginDto->setPassword('password');

        $response = $authService->login($loginDto);

        $data = $response->getData(true);

        $this->assertEquals('Invalid email or password', $data['msg']);
        $this->assertEquals(401, $data['code']);
    }
}
