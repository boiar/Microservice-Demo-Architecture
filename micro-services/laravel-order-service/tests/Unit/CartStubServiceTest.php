<?php

namespace Tests\Unit;

use App\DTOs\AddItemToCartDTO;
use App\Services\CartService;
use Tests\Stubs\CartStubService;
use Tests\TestCase;

class CartStubServiceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->bind(CartService::class, CartStubService::class);
    }


    public function test_add_to_cartSuccessfully(): void
    {
        $dto = new AddItemToCartDTO();
        $dto->setProductId(10);
        $dto->setQuantity(2);

        $cartService = app(CartService::class);

        $res = $cartService->addToCart($dto);

        $this->assertEquals(10, $res->product_id);
        $this->assertEquals(2, $res->quantity);
        $this->assertEquals(1, $res->user_id);
    }


    public function test_get_cart_items_after_add(){
        $dto = new AddItemToCartDTO();
        $dto->setProductId(20);
        $dto->setQuantity(1);

        $cartService = app(CartService::class);
        $cartService->addToCart($dto);

        $cartItems = $cartService->getCartItems();

        $this->assertEquals(200, $cartItems->code);
        $this->assertCount(1, $cartItems->data);
        $this->assertEquals(20, $cartItems->data[0]['product_id']);
    }

    public function test_add_invalid_product()
    {
        $dto = new AddItemToCartDTO();
        $dto->setProductId(999);
        $dto->setQuantity(1);

        $cartService = app(CartService::class);
        $response    = $cartService->addToCart($dto);

        $this->assertEquals(404, $response->code);
        $this->assertEquals('Invalid Product', $response->message);
    }



    public function test_add_invalid_qty_product()
    {
        $dto = new AddItemToCartDTO();
        $dto->setProductId(20);
        $dto->setQuantity(200);

        $cartService = app(CartService::class);
        $response    = $cartService->addToCart($dto);

        $this->assertEquals(400, $response->code);
        $this->assertEquals('Quantity exceeds available stock.', $response->message);
    }


    public function test_remove_item_from_cart()
    {
        $dto = new AddItemToCartDTO();
        $dto->setProductId(10);
        $dto->setQuantity(2);

        $cartService = app(CartService::class);
        $item = $cartService->addToCart($dto);

        $response = $cartService->removeFromCart($item->id);

        $this->assertEquals(200, $response->code);
        $this->assertEquals('Item removed from cart successfully', $response->message);
    }


    public function test_remove_non_exist_item_from_cart()
    {

        $cartService = app(CartService::class);

        $response = $cartService->removeFromCart(999); // not exist

        $this->assertEquals(404, $response->code);
        $this->assertEquals('Item not found in cart', $response->message);
    }



}
