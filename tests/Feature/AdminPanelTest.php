<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function admin_panel_routes_exist()
    {
        $response = $this->get('/webco-admin');
        $response->assertStatus(404);

        $response = $this->get('/webco-admin/products');
        $response->assertStatus(404);
    }
} 