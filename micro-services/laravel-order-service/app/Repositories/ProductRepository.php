<?php

namespace App\Repositories;

use App\Contracts\Repositories\IProductRepository;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepository implements IProductRepository
{
    public function findById(int|string $id): ?Product
    {
        return Product::find($id);
    }

    public function findByIdsWithLock(array $ids): Collection
    {
        return Product::whereIn('id', $ids)
                      ->lockForUpdate()
                      ->get()
                      ->keyBy('id');
    }

    public function getAll(): Collection
    {
        return Product::all();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int|string $id, array $data): Product
    {
        $product = $this->findById($id);
        if (!$product) {
            abort(404, 'Product not found');
        }

        $product->update($data);
        return $product;
    }

    public function delete(int|string $id): bool
    {
        return Product::where('id', $id)->delete() > 0;
    }
}
