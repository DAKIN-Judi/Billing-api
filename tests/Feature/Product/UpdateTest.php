<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use App\Models\Product;

class ProductUpdateTest extends TestCase
{
    public function test_should_fail_when_data_provided_is_invalid()
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->putJson('/api/products/' . $product->id, ['unit_price' => 'price']);

        $response->assertStatus(400);
    }

    public function test_should_update_product_successfully()
    {
        $product = Product::factory()->create();

        $payload = [
            'designation' => 'Updated Product',
            'description' => 'Updated description',
            'unit_price' => 200,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->putJson('/api/products/' . $product->id, $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'designation' => 'Updated Product',
                     'unit_price' => 200,
                 ]);
    }
}
