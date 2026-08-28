<?php

namespace App\Services;

use App\Contracts\Repositories\ICartRepository;
use App\Contracts\Services\ICartService;
use App\Contracts\Services\IJwtService;
use App\Contracts\Services\IProductGrpcService;
use App\DTOs\AddItemToCartDTO;
use App\Helpers\ResponseHelper;

class CartService implements ICartService
{
    protected ICartRepository $cartRepo;
    protected IProductGrpcService $productGrpc;
    protected IJwtService $jwtService;


    public function __construct(
        ICartRepository $cartRepo,
        IProductGrpcService $productGrpc,
        IJwtService $jwtService
    )
    {
        $this->cartRepo = $cartRepo;
        $this->productGrpc = $productGrpc;
        $this->jwtService = $jwtService;
    }

    public function getCartItems(): object
    {
        $userId    = $this->jwtService->getUserIdFromToken();
        $cartItems = $this->cartRepo->getUserCartItems($userId);
        return ResponseHelper::returnData($cartItems);
    }

    public function addToCart(AddItemToCartDTO $dto): object
    {
        $userId = $this->jwtService->getUserIdFromToken();

        $product = $this->productGrpc->getProductById($dto->getProductId());
        if (!$product) {
            return ResponseHelper::returnError(404, 'Invalid product');
        }

        $existingItem = $this->cartRepo->findUserCartItem($userId, $dto->getProductId());

        $requestedQty = $dto->getQuantity();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $requestedQty;

            $this->cartRepo->updateQuantity($existingItem, $newQty);

            // Refresh snapshot in case price changed since it was first added
            $this->cartRepo->update($existingItem->id, [
                'product_name' => $product->name,
                'price'        => $product->price,
            ]);

            return ResponseHelper::returnSuccessMessage('Item added to cart', 201);
        }

        $this->cartRepo->create([
            'user_id'      => $userId,
            'product_id'   => $dto->getProductId(),
            'quantity'     => $requestedQty,
            'product_name' => $product->name,
            'price'        => $product->price,
        ]);

        return ResponseHelper::returnSuccessMessage('Item added to cart', 201);    }

    public function removeFromCart(int $itemId): object
    {
        $userId = $this->jwtService->getUserIdFromToken();
        $deleted = $this->cartRepo->deleteItem($userId, $itemId);

        if (!$deleted) {
            return ResponseHelper::returnError(404, 'Item not found in cart');
        }

        return ResponseHelper::returnSuccessMessage('Item removed from cart successfully', 200);
    }


}
