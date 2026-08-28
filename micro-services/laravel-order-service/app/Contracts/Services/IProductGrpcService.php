<?php

namespace App\Contracts\Services;

interface IProductGrpcService
{
    /**
     * Fetch a product snapshot from the Product Service over gRPC.
     */
    public function getProductById(int $id): ?object;

    /**
     * Fetch multiple product snapshots from the Product Service over gRPC.
     */
    public function getProductsByIds(array $ids): ?array;
}