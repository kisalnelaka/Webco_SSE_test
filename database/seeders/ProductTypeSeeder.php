<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Standard',
                'bonus' => 0
            ],
            [
                'name' => 'Premium',
                'bonus' => 10
            ],
            [
                'name' => 'Deluxe',
                'bonus' => 20
            ],
            [
                'name' => 'Limited Edition',
                'bonus' => 30
            ],
            [
                'name' => 'Exclusive',
                'bonus' => 50
            ],
        ];

        foreach ($types as $type) {
            ProductType::create($type);
        }
    }
} 