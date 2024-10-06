<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use App\Models\Product;

class DestroyTest extends TestCase
{
    public function test_should_fail_when_product_does_not_exist()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->deleteJson('/api/products/9999');

        $response->assertStatus(404);
    }

    public function test_should_delete_product_successfully()
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->deleteJson('/api/products/' . $product->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Product deleted successfully',
                 ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
