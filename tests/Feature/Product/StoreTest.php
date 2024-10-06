<?php

namespace Tests\Feature\Product;

use Tests\TestCase;

class StoreTest extends TestCase
{
    public function test_should_fail_when_data_is_missing()
    {
        $payload = [];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->postJson('/api/products', $payload);

        $response->assertStatus(400);
    }

    public function test_should_create_product_successfully()
    {
        $payload = [
            'designation' => 'Product 1',
            'description' => 'Description of product 1',
            'unit_price' => 100,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->postJson('/api/products', $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'designation' => 'Product 1',
                     'unit_price' => 100,
                 ]);
    }
}
