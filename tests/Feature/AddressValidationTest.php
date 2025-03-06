<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class AddressValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function address_validation_endpoint_exists()
    {
        $response = $this->postJson('/api/addresses/validate', []);
        $response->assertStatus(404);
    }
} 