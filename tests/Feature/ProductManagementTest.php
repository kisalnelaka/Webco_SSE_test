<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function basic_product_endpoints_exist()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(404);

        $response = $this->postJson('/api/products', []);
        $response->assertStatus(404);
    }
} 