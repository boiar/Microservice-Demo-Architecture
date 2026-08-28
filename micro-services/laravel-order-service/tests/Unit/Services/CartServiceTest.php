<?php

namespace Tests\Unit\Services;
use App\Contracts\Repositories\ICartRepository;
use App\Contracts\Repositories\IProductRepository;
use App\Contracts\Services\IJwtService;
use App\DTOs\AddItemToCartDTO;
use App\Helpers\JwtHelper;
use App\Services\CartService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use Tests\Unit\Stubs\CartRepositoryStub;
use Tests\Unit\Stubs\ProductRepositoryStub;


class CartServiceTest extends TestCase
{
    use WithFaker;

    private CartService $cartService;
    private ICartRepository $cartRepo;


    protected function setUp(): void
    {
        parent::setUp();

        // Bind stubs
        $this->app->bind(ICartRepository::class, CartRepositoryStub::class);
        $this->app->bind(IProductRepository::class, ProductRepositoryStub::class);

        $jwtMock = Mockery::mock(IJwtService::class);
        $jwtMock->shouldReceive('getUserFromToken')
                ->andReturn(['id' => 1, 'email' => 'test@example.com']);

        $jwtMock->shouldReceive('getUserIdFromToken')
                ->andReturn(1);

        $this->app->instance(IJwtService::class, $jwtMock);

        // Resolve service and dependencies
        $this->cartService = app(CartService::class);
        $this->cartRepo = app(ICartRepository::class);

    }

    protected function tearDown(): void
    {
        Mockery::close(); // Close all Mockery mocks
        parent::tearDown();
    }


    public function test_get_cart_items_successfully()
    {
        $response = $this->cartService->getCartItems();
        $data = $response->getData(true);

        $this->assertTrue($data['status']);
        $this->assertIsArray($data['data']);
        $this->assertEquals(200, $data['code']);

    }

    public function test_cart_is_empty()
    {
        $userId = 1;
        $this->cartRepo->clearUserCart($userId);
        $response = $this->cartService->getCartItems();
        $data = $response->getData(true);
        $this->assertEmpty($data['data']);
    }

    public function test_add_to_cart_successfully()
    {
        $dto = new AddItemToCartDTO();
        $dto->setProductId(1);
        $dto->setQuantity(1);

        $response = $this->cartService->addToCart($dto);
        $data = $response->getData(true);

        $this->assertTrue($data['status']);
        $this->assertEquals(201, $data['code']);
        $this->assertEquals('Item added to cart', $data['msg']);
    }

    public function test_add_to_cart_exceeds_stock()
    {
        $dto = new AddItemToCartDTO();
        $dto->setProductId(1);
        $dto->setQuantity(999);

        $response = $this->cartService->addToCart($dto);
        $data = $response->getData(true);

        $this->assertFalse($data['status']);
        $this->assertEquals(400, $data['code']);
        $this->assertEquals('Quantity exceeds available stock.', $data['msg']);
    }

    public function test_add_to_cart_invalid_product()
    {
        $dto = new AddItemToCartDTO();
        $dto->setProductId(999); // not found
        $dto->setQuantity(1);

        $response = $this->cartService->addToCart($dto);
        $data = $response->getData(true);

        $this->assertFalse($data['status']);
        $this->assertEquals(404, $data['code']);
        $this->assertEquals('Invalid product', $data['msg']);
    }

    public function test_remove_from_cart_successfully()
    {
        $response = $this->cartService->removeFromCart(1);
        $data = $response->getData(true);

        $this->assertTrue($data['status']);
        $this->assertEquals(200, $data['code']);
        $this->assertEquals('Item removed from cart successfully', $data['msg']);
    }

    public function test_remove_from_cart_not_found()
    {
        $response = $this->cartService->removeFromCart(999); // not exist
        $data = $response->getData(true);

        $this->assertFalse($data['status']);
        $this->assertEquals(404, $data['code']);
        $this->assertEquals('Item not found in cart', $data['msg']);
    }
}
