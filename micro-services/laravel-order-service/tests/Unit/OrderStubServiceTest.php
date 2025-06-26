<?php
namespace Tests\Unit;

use App\Contracts\IOrder;
use App\DTOs\CreateOrderDTO;
use App\Services\CartService;
use App\Services\OrderService;
use Tests\Stubs\CartStubService;
use Tests\Stubs\OrderStubService;
use Tests\TestCase;

class OrderStubServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->bind(OrderService::class, OrderStubService::class);
    }

    public function test_create_order_successfully()
    {
        $dto = new CreateOrderDTO();
        $dto->setAddress('Test Address');
        $dto->setNotes('Handle with care');

        $service = app(IOrder::class);
        $res = $service->createOrder($dto);
        $this->assertTrue($res->status);
        $this->assertEquals('Test Address', $res->data['order']['address']);
        $this->assertCount(1, $res->data['items']);
    }
}
