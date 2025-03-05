<?php

namespace Database\Seeders;

use App\Models\ProductColor;
use Illuminate\Database\Seeder;

class ProductColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Red', 'hex_code' => '#FF0000'],
            ['name' => 'Blue', 'hex_code' => '#0000FF'],
            ['name' => 'Green', 'hex_code' => '#00FF00'],
            ['name' => 'Yellow', 'hex_code' => '#FFFF00'],
            ['name' => 'Purple', 'hex_code' => '#800080'],
            ['name' => 'Orange', 'hex_code' => '#FFA500'],
            ['name' => 'Black', 'hex_code' => '#000000'],
            ['name' => 'White', 'hex_code' => '#FFFFFF'],
            ['name' => 'Gray', 'hex_code' => '#808080'],
            ['name' => 'Pink', 'hex_code' => '#FFC0CB'],
        ];

        foreach ($colors as $color) {
            ProductColor::create($color);
        }
    }
}