<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_invoices_list_success()
    {
        Invoice::factory()->count(5)->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->getJson('/api/invoices');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'customer_id', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_get_invoices_list_failure()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->getJson('/api/invoices');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }
}
