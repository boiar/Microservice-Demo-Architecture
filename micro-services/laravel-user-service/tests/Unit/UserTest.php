<?php

namespace Tests\Unit;

use App\DTOs\UpdateUserProfileDTO;
use Tests\Stubs\Services\UserStubService;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        $this->app->bind(\App\Services\UserService::class, UserStubService::class);
    }


    public function test_update_profile_successfully(): void
    {
        $user = new User([
             'id' => 1,
             'name' => 'Old Name',
             'email' => 'user@example.com',
             'password' => 'old_password'
        ]);

        UserStubService::setAuthenticatedUser($user); // set as logged-in user

        $dto = new UpdateUserProfileDTO();
        $dto->setName('New Name');
        $dto->setPassword('new_password');

        $service = new UserStubService();
        $response = $service->updateProfile($dto);
        $data = $response->getData(true);

        $this->assertEquals('Profile updated successfully', $data['msg']);
        $this->assertEquals('New Name', $data['data']['user']['name']);
    }






}
