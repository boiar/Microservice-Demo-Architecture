<?php

namespace Tests\Feature;

use App\Helpers\JwtHelper;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
{
    use RefreshDatabase;


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

        Redis::shouldReceive('publish')->andReturn(true);
    }

    protected function createUserAndToken()
    {
        $user = User::create([
             'name' => 'Order Tester',
             'email' => 'order@test.com',
             'password' => bcrypt('password'),
        ]);

        $token = JwtHelper::generateToken($user);

        return [$user, $token];
    }


    public function test_guest_cannot_create_order()
    {
        $response = $this->postJson('/api/order', [
            'address' => 'Test Address',
        ]);

        $response->assertStatus(401);
    }



    public function test_user_can_create_order()
    {
        [$user, $token] = $this->createUserAndToken();

        $product = Product::create([
           'name' => 'Test Product',
           'price' => 50,
           'qty' => 100,
           'description' => 'description',
        ]);

        Cart::create([
             'user_id' => $user->id,
             'product_id' => $product->id,
             'quantity' => 2,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/order', [
                             'address' => 'Test Address',
                         ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
           'status',
           'code',
           'msg',
           'data' => [
               'order' => [
                   'id',
                   'user_id',
                   'total_price',
                   'status',
                   'created_at',
                   'updated_at',
                   'address',
                   'note',
               ],
               'items' => [
                   [
                       'order_id',
                       'product_id',
                       'quantity',
                       'price',
                       'created_at',
                       'updated_at',
                   ]
               ]
           ]
        ]);

    }

    public function test_user_cannot_create_order_with_empty_cart()
    {
        [$user, $token] = $this->createUserAndToken();

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/order', [
                             'address' => 'Test Address',
                         ]);

        $response->assertStatus(400);
        $response->assertJson([
              'status' => false,
        ]);
    }


    public function test_user_cart_is_cleared_after_order()
    {
        [$user, $token] = $this->createUserAndToken();

        $product = Product::create([
           'name' => 'Test Product',
           'price' => 50,
           'qty' => 100,
           'description' => 'desc',
        ]);

        Cart::create([
             'user_id' => $user->id,
             'product_id' => $product->id,
             'quantity' => 2,
        ]);

        $this->withHeader('Authorization', "Bearer $token")
             ->postJson('/api/order', [
                 'address' => 'Test Address',
             ])
             ->assertStatus(200);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
        ]);
    }

}
