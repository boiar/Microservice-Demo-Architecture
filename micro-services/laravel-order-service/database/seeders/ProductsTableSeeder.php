<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Product 1',  'price' => 50,     'qty' => 100, 'description' => 'Description for product 1'],
            ['name' => 'Product 2',  'price' => 100,    'qty' => 50,  'description' => 'Description for product 2'],
            ['name' => 'Product 3',  'price' => 100,    'qty' => 75,  'description' => 'Description for product 3'],
            ['name' => 'Product 4',  'price' => 1000,   'qty' => 120, 'description' => 'Description for product 4'],
            ['name' => 'Product 5',  'price' => 250,    'qty' => 200, 'description' => 'Description for product 5'],
            ['name' => 'Product 6',  'price' => 350,    'qty' => 60,  'description' => 'Description for product 6'],
            ['name' => 'Product 7',  'price' => 400,    'qty' => 80,  'description' => 'Description for product 7'],
            ['name' => 'Product 8',  'price' => 450,    'qty' => 90,  'description' => 'Description for product 8'],
            ['name' => 'Product 9',  'price' => 500,    'qty' => 30,  'description' => 'Description for product 9'],
            ['name' => 'Product 10', 'price' => 55.99,  'qty' => 150, 'description' => 'Description for product 10'],
        ];

        Product::insert($products);
    }
}
