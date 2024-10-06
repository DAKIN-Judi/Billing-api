<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_show_invoice_details_success()
    {
        $invoice = Invoice::factory()->create();
        $response = $this->getJson("/api/invoices/{$invoice->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'customer_id', 'created_at', 'updated_at']
            ]);
    }

    public function test_show_invoice_details_failure()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->getJson("/api/invoices/99999");
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Record not found'
            ]);
    }
}
