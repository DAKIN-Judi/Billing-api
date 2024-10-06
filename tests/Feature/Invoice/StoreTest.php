<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_invoice_success()
    {
        $customer = Customer::factory()->create();
        $products = Product::factory()->count(2)->create();
        $data = [
            'name' => 'Invoice 001',
            'customer_id' => $customer->id,
            'products' => $products
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->postJson('/api/invoices', $data);

        $response->dump();
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'customer_id', 'created_at', 'updated_at']
            ]);
    }

    public function test_create_invoice_failure()
    {
        $data = [
            'name' => '',
            'customer_id' => 99999,
            'products' => []
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->postJson('/api/invoices', $data);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }
}
