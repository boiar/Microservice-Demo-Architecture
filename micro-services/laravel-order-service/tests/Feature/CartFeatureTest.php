<?php

namespace Tests\Feature;

use App\Helpers\JwtHelper;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartFeatureTest extends TestCase
{
    use RefreshDatabase;


    protected function actingAsJwt(User $user)
    {
        $token = auth('api')->login($user);
        return $this->withHeader('Authorization', "Bearer $token");
    }


    public function test_guest_cannot_access_cart()
    {
        $response = $this->getJson('/api/cart');
        $response->assertStatus(401);
    }

    public function test_user_can_get_empty_cart()
    {
        $user = User::factory()->create();
        $token = JwtHelper::generateToken($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/cart');

        $response->assertStatus(200);
        $response->assertJson([
          'status' => true,
          'data' => [],
        ]);
    }

    public function test_user_can_add_item_to_cart()
    {
        $user = \App\Models\User::create([
             'name' => 'Test User',
             'email' => 'test@example.com',
             'password' => bcrypt('password123'),
        ]);

        $product = \App\Models\Product::create([
           'name' => 'Test Product',
           'price' => 50.00,
           'qty' => 10,
           'description' => 'description',
       ]);

        $token = \App\Helpers\JwtHelper::generateToken($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/cart/add', [
                             'product_id' => $product->id,
                             'quantity' => 2,
                         ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
           'id',
           'product_id',
           'quantity',
        ]);
    }



    public function test_add_to_cart_validation_fails()
    {
        $user = \App\Models\User::create([
             'name' => 'Test User',
             'email' => 'test@example.com',
             'password' => bcrypt('password123'),
        ]);

        $token = JwtHelper::generateToken($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/cart/add', [
                             'product_id' => null,
                             'quantity' => 0,
                         ]);

        $response->assertStatus(400);
        $response->assertJsonFragment([
          'errors' => [
              "The product id field is required.",
              "The quantity field must be at least 1.",
          ]
        ]);
    }

    public function test_user_can_remove_item_from_cart()
    {
        $user = User::create([
             'name' => 'Test User',
             'email' => 'test@example.com',
             'password' => bcrypt('password'),
        ]);

        // Create product with required stock info
        $product = Product::create([
           'name' => 'Test Product',
           'price' => 25,
           'qty' => 10,
           'description' => 'description',
        ]);

        // Create a cart item directly in DB
        $cartItem = Cart::create([
             'user_id' => $user->id,
             'product_id' => $product->id,
             'quantity' => 2,
        ]);

        // Generate JWT token for the user
        $token = JwtHelper::generateToken($user);

        // Make request
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->deleteJson("/api/cart/delete/{$cartItem->id}");

        $response->assertStatus(200);

        // Optionally assert response structure or message
        $response->assertJsonFragment(['msg' => 'Item removed from cart successfully',]);
    }



}
