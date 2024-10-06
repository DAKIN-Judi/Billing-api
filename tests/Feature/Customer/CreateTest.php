<?php

namespace Tests\Feature\Customer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTest extends TestCase
{

    use RefreshDatabase;

    protected $bearerToken;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $this->bearerToken = JWTAuth::fromUser($user);
        $this->user = $user;
    }

    public function test_creates_a_customer_successfully()
    {

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/customers', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'billingAddress' => '123 Main St, City, Country',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Customer created successfully',
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'billingAddress' => '123 Main St, City, Country',
        ]);
    }

    public function test_fails_to_create_a_customer_due_to_validation_errors()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->postJson('/api/customers', [
            'name' => '',
            'email' => '',
            'billingAddress' => '123 Main St, City, Country',
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['name', 'email']);
    }
}
