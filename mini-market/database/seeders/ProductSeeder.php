<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Ekmek',
            'price' => 15,
            'stock' => 50,
        ]);

        Product::create([
            'name' => 'Süt',
            'price' => 35,
            'stock' => 20,
        ]);

        Product::create([
            'name' => 'Yumurta',
            'price' => 80,
            'stock' => 30,
        ]);
    }
}