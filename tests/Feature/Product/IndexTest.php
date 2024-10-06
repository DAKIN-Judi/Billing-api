<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTest extends TestCase
{
    use RefreshDatabase;
    public function test_should_return_empty_list_if_no_products_exist()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(0, 'data');
    }

    public function test_should_return_paginated_products()
    {
        Product::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'designation', 'description', 'unit_price']
                     ]
                 ]);
    }
}
