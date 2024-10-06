<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected $bearerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->bearerToken = JWTAuth::fromUser($user);
    }

    public function test_returns_a_customer_by_id()
    {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->getJson('/api/customers/' . $customer->id);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'email', 'billingAddress', 'created_at', 'updated_at'
            ]
        ]);
    }

    public function test_returns_404_if_customer_not_found()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->getJson('/api/customers/9999');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Record not found.'
        ]);
    }
}
