<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_invoice_success()
    {
        $invoice = Invoice::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->deleteJson("/api/invoices/{$invoice->id}");
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);
    }

    public function test_delete_invoice_failure()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAuthToken(),
        ])->deleteJson("/api/invoices/99999");
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Record not found.'
            ]);
    }
}
