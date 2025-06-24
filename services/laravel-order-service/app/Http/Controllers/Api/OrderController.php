<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        return $this->orderService->getUserOrders();
    }

    public function show(int $orderId)
    {
        return $this->orderService->getOrderDetails($orderId);
    }

    public function store(CreateOrderRequest $request): ?object
    {
        return $this->orderService->createOrder($request->getDto());
    }
}
