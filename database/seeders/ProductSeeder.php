<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartphone X1',
                'description' => 'Latest smartphone with advanced features',
                'category_id' => 1, // Electronics
                'product_color_id' => 7, // Black
                'types' => [1, 2] // Standard and Premium
            ],
            [
                'name' => 'Designer T-Shirt',
                'description' => 'Comfortable cotton t-shirt with unique design',
                'category_id' => 2, // Clothing
                'product_color_id' => 1, // Red
                'types' => [1, 3] // Standard and Deluxe
            ],
            [
                'name' => 'Garden Chair',
                'description' => 'Durable outdoor chair for your garden',
                'category_id' => 3, // Home & Garden
                'product_color_id' => 3, // Green
                'types' => [1, 2, 3] // Standard, Premium, and Deluxe
            ],
            [
                'name' => 'Tennis Racket Pro',
                'description' => 'Professional grade tennis racket',
                'category_id' => 4, // Sports
                'product_color_id' => 2, // Blue
                'types' => [2, 4] // Premium and Limited Edition
            ],
            [
                'name' => 'Programming Guide 2024',
                'description' => 'Comprehensive programming guide',
                'category_id' => 5, // Books
                'product_color_id' => 8, // White
                'types' => [1, 5] // Standard and Exclusive
            ],
        ];

        foreach ($products as $productData) {
            $types = $productData['types'];
            unset($productData['types']);
            
            $product = Product::create($productData);
            
            // Attach product types
            $product->productTypes()->attach($types);
        }
    }
} 