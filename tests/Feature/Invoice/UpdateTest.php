<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_invoice_success()
    {
        $invoice = Invoice::factory()->create();
        $data = ['name' => 'Updated Invoice'];
        $response = $this->putJson("/api/invoices/{$invoice->id}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'customer_id', 'created_at', 'updated_at']
            ]);
    }

    public function test_update_invoice_failure()
    {
        $response = $this->putJson("/api/invoices/99999", ['name' => 'Non-existent Invoice']);
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Record not found'
            ]);
    }
}
