<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);


        $response->assertStatus(200);
        $response->assertJsonStructure([
           'code',
           'msg',
           'data' => [
               'user' => ['id', 'name', 'email'],
               'token',
               'refresh_token'
           ]
        ]);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'not_matching',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['errors' => [
            "The name field is required.",
            "The email field must be a valid email address.",
            "The password field must be at least 6 characters.",
            "The password field confirmation does not match.",
        ]]);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $this->withoutExceptionHandling();

        $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);


        $response->assertStatus(200);
        $response->assertJsonStructure([
           'code',
           'msg',
           'data' => [
               'user' => ['id', 'name', 'email'],
               'token',
               'refresh_token'
           ]
        ]);
    }

    public function test_user_cannot_login_with_wrong_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
              'code' => 401,
              'msg' => 'Invalid email or password',
        ]);
    }




}
