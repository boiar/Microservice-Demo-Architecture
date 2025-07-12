<?php

namespace Tests\Unit\Stubs;

use App\Contracts\Repositories\IProductRepository;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepositoryStub implements IProductRepository
{
    private array $products;

    public function __construct()
    {
        $this->products = [
            1 => (new Product())->forceFill(['id' => 1, 'name' => 'Sample Product', 'price' => 10, 'qty' => 10]),
            2 => (new Product())->forceFill(['id' => 2, 'name' => 'Another Product', 'price' => 20, 'qty' => 5]),
            100 => (new Product())->forceFill(['id' => 100, 'name' => 'Cart Product', 'price' => 15, 'qty' => 50]),
        ];
    }

    public function findById(int|string $id): ?Product
    {
        return $this->products[$id] ?? null;
    }

    public function findByIdsWithLock(array $ids): Collection
    {
        return collect($this->products)->only($ids)->keyBy('id');
    }

    public function getAll(): Collection
    {
        return collect($this->products)->keyBy('id');
    }

    public function create(array $data): Product
    {
        $product = new Product($data);
        $this->products[$data['id']] = $product;
        return $product;
    }

    public function update(int|string $id, array $data): Product
    {
        $product = $this->findById($id);
        foreach ($data as $key => $value) {
            $product->{$key} = $value;
        }
        return $product;
    }

    public function delete(int|string $id): bool
    {
        unset($this->products[$id]);
        return true;
    }
}
