<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items'
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Items for home and garden'
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports equipment and accessories'
            ],
            [
                'name' => 'Books',
                'description' => 'Books and publications'
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
} 