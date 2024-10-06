<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $bearerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->bearerToken = JWTAuth::fromUser($user);
    }

    public function test_updates_a_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->putJson('/api/customers/' . $customer->id, [
            'name' => 'John Updated',
            'email' => 'john.updated@example.com',
            'billingAddress' => '4567 Updated St',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Customer updated successfully',
            'data' => [
                'id' => $customer->id,
                'name' => 'John Updated',
                'email' => 'john.updated@example.com',
                'billingAddress' => '4567 Updated St',
            ]
        ]);
    }

    public function test_returns_404_if_customer_not_found_when_updating()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->putJson('/api/customers/9999', [
            'name' => 'John Updated',
            'email' => 'john.updated@example.com',
            'billingAddress' => '4567 Updated St',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Record not found.'
        ]);
    }
}
