<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

interface IProductRepository
{
    public function findById(int|string $id): ?Product;

    public function findByIdsWithLock(array $ids): Collection;

    public function getAll(): Collection;

    public function create(array $data): Product;

    public function update(int|string $id, array $data): Product;

    public function delete(int|string $id): bool;
}
