<?php


namespace Tests\Unit\Services;

use App\Contracts\Repositories\ICartRepository;
use App\Contracts\Repositories\IOrderItemRepository;
use App\Contracts\Repositories\IOrderRepository;
use App\Contracts\Repositories\IProductRepository;
use App\Contracts\Services\IJwtService;
use App\DTOs\CreateOrderDTO;
use App\Helpers\JwtHelper;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Tests\TestCase;
use Tests\Unit\Stubs\CartRepositoryStub;
use Tests\Unit\Stubs\OrderItemRepositoryStub;
use Tests\Unit\Stubs\OrderRepositoryStub;
use Tests\Unit\Stubs\ProductRepositoryStub;

class OrderServiceTest extends TestCase
{
    use WithFaker;

    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(IOrderRepository::class, OrderRepositoryStub::class);
        $this->app->bind(IOrderItemRepository::class, OrderItemRepositoryStub::class);
        $this->app->bind(IProductRepository::class, ProductRepositoryStub::class);
        $this->app->bind(ICartRepository::class, CartRepositoryStub::class);
        $this->cartRepo = app(ICartRepository::class);

        $jwtMock = Mockery::mock(IJwtService::class);
        $jwtMock->shouldReceive('getUserFromToken')
                ->andReturn(['id' => 1, 'email' => 'test@example.com']);

        $jwtMock->shouldReceive('getUserIdFromToken')
                ->andReturn(1);

        $this->app->instance(IJwtService::class, $jwtMock);

        $this->orderService = app(OrderService::class);
        $this->cartService = app(CartService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_user_orders_successfully()
    {
        $response = $this->orderService->getUserOrders();
        $data     = $response->getData(true);

        $this->assertTrue($data['status']);
        $this->assertIsArray($data['data']);
        $this->assertEquals(200, $data['code']);
    }

    public function test_get_order_details_successfully()
    {
        $response = $this->orderService->getOrderDetails(1);
        $data     = $response->getData(true);

        $this->assertTrue($data['status']);
        $this->assertEquals(200, $data['code']);
        $this->assertArrayHasKey('order', $data['data']);
        $this->assertArrayHasKey('items', $data['data']);
    }

    public function test_get_order_details_unauthorized()
    {
        $response = $this->orderService->getOrderDetails(999); // Not owned
        $data     = $response->getData(true);

        $this->assertFalse($data['status']);
        $this->assertEquals(403, $data['code']);
        $this->assertEquals('This action is unauthorized.', $data['msg']);
    }

    public function test_create_order_successfully()
    {
        Redis::shouldReceive('publish')->once()->andReturn(true);
        $dto = new CreateOrderDTO();
        $dto->setAddress('123 Test Street');
        $dto->setNotes('Leave at door');

        $response = $this->orderService->createOrder($dto);
        $data     = $response->getData(true);

        $this->assertTrue($data['status']);
        $this->assertEquals(200, $data['code']);
        $this->assertArrayHasKey('order', $data['data']);
        $this->assertArrayHasKey('items', $data['data']);
    }

    public function test_create_order_cart_is_empty()
    {
        $userId = 1;
        $this->cartRepo->clearUserCart($userId);

        $dto = new CreateOrderDTO();
        $dto->setAddress('Test Address');
        $dto->setNotes('No note');

        $response = $this->orderService->createOrder($dto);
        $data     = $response->getData(true);

        $this->assertFalse($data['status']);
        $this->assertEquals(400, $data['code']);
        $this->assertEquals('Cart is empty', $data['msg']);
    }
}
