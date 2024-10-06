<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use App\Models\Product;

class ShowTest extends TestCase
{
    public function test_should_fail_when_product_does_not_exist()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->getJson('/api/products/9999');

        $response->assertStatus(404);
    }

    public function test_should_return_product_details_successfully()
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->getJson('/api/products/' . $product->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'designation' => $product->designation,
                     'unit_price' => $product->unit_price,
                 ]);
    }
}
