<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected $bearerToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur et générer un token JWT
        $user = User::factory()->create();
        $this->bearerToken = JWTAuth::fromUser($user);
    }

    /** @test */
    public function it_returns_a_list_of_customers()
    {
        Customer::factory()->count(40)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
        ])->getJson('/api/customers');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'name', 'email', 'billingAddress', 'created_at', 'updated_at'
                ]
            ]
        ]);
    }

    /** @test */
    public function it_returns_unauthenticated_if_token_is_invalid()
    {
        // Envoyer une requête GET avec un token invalide
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->getJson('/api/customers');

        // Vérifier que la réponse a un statut 401 (Unauthenticated)
        $response->assertStatus(401);

        // Vérifier que le message de l'erreur est "Unauthenticated."
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }
}
