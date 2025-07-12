<?php

namespace App\Services;

use App\Contracts\Repositories\ICartRepository;
use App\Contracts\Repositories\IProductRepository;
use App\Contracts\Services\ICartService;
use App\Contracts\Services\IJwtService;
use App\DTOs\AddItemToCartDTO;
use App\Helpers\ResponseHelper;

class CartService implements ICartService
{
    protected ICartRepository $cartRepo;
    protected IProductRepository $productRepo;
    protected IJwtService $jwtService;


    public function __construct(
        ICartRepository $cartRepo,
        IProductRepository $productRepo,
        IJwtService $jwtService
    )
    {
        $this->cartRepo = $cartRepo;
        $this->productRepo = $productRepo;
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


        $product = $this->productRepo->findById($dto->getProductId());
        if (!$product) {
            return ResponseHelper::returnError(404, 'Invalid product');
        }

        $existingItem = $this->cartRepo->findUserCartItem($userId, $dto->getProductId());

        $requestedQty = $dto->getQuantity();
        $existingQty  = $existingItem ? $existingItem->quantity : 0;
        $totalQty     = $requestedQty + $existingQty;

        if ($totalQty > $product->qty) {
            return ResponseHelper::returnError(400, 'Quantity exceeds available stock.');
        }

        if ($existingItem) {
            $this->cartRepo->updateQuantity($existingItem, $totalQty);
            return ResponseHelper::returnSuccessMessage('Item added to cart', 201);
        }

        $this->cartRepo->create([
            'user_id'    => $userId,
            'product_id' => $dto->getProductId(),
            'quantity'   => $requestedQty,
        ]);

        return ResponseHelper::returnSuccessMessage('Item added to cart', 201);
    }

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
