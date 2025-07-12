<?php

namespace Tests\Feature;

use App\Helpers\JwtHelper;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class CartFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::shouldReceive('publish')->andReturn(true);
    }




    public function test_guest_cannot_access_cart()
    {
        $response = $this->getJson('/api/cart');
        $response->assertStatus(401);
    }

    public function test_user_can_get_empty_cart()
    {
        [$user, $token] = $this->createUserAndToken();

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
        [$user, $token] = $this->createUserAndToken();

        $product = Product::create([
               'name'        => 'Test Product',
               'description' => 'Test description',
               'price'       => 100,
               'qty'         => 50,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/cart/add', [
                             'product_id' => $product->id,
                             'quantity' => 2,
                         ]);

        $response->assertStatus(201);
        $response->assertJson(['status' => true]);
    }

    public function test_add_to_cart_validation_fails()
    {
        [$user, $token] = $this->createUserAndToken();

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/cart/add', [
                             // missing product_id and quantity
                         ]);

        $response->assertStatus(400);
    }

    public function test_user_can_remove_item_from_cart()
    {
        [$user, $token] = $this->createUserAndToken();

        $product = Product::create([
           'name'        => 'Test Product',
           'description' => 'Test description',
           'price'       => 100,
           'qty'         => 50,
        ]);

        Cart::create([
             'user_id' => $user->id,
             'product_id' => $product->id,
             'quantity' => 3,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->deleteJson("/api/cart/delete/{$product->id}");

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
    }
}
