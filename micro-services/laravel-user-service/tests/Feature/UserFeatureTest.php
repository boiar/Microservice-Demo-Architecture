<?php

namespace tests\Feature;

use App\Contracts\Services\IJwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected IJwtService $jwtService;

    protected function setUp(): void
    {
        parent::setUp();

        // resolve jwt service from container
        $this->jwtService = $this->app->make(IJwtService::class);
    }


    public function test_authenticated_user_can_update_profile()
    {
        $this->withoutExceptionHandling();


        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);

        $token = $this->jwtService->generateToken($user);

        $response = $this->withHeaders([
           'Authorization' => "Bearer $token",
        ])->postJson('/api/user/profile', [
            'name' => 'Updated Name',
            'password' => 'newsecurepassword',
            'password_confirmation' => 'newsecurepassword',
        ]);


        $response->assertStatus(200);

        $response->assertJsonStructure([
           'code',
           'msg',
           'data' => [
               'user' => ['id', 'name', 'email'],
           ],
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);

        $this->assertTrue(Hash::check('newsecurepassword', $user->fresh()->password));

    }

   public function test_user_can_update_profile_name_only()
    {
        $this->withoutExceptionHandling();

        $user = \App\Models\User::factory()->create();

        $token = $this->jwtService->generateToken($user);

        $response = $this->withHeaders([
           'Authorization' => "Bearer $token",
        ])->postJson('/api/user/profile', [
            'name' => 'New Only Name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Only Name',
        ]);
    }





}
