<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::factory()->create([
            'email' => 'test@fams.test',
            'name' => 'Test User',
        ]);
        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    public function test_can_get_profile(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'test@fams.test',
                ],
            ]);
    }

    public function test_can_update_profile(): void
    {
        $response = $this->withToken($this->token)->putJson('/api/v1/profile', [
            'name' => 'Updated Name',
            'phone' => '08123456789',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'phone' => '08123456789',
        ]);
    }

    public function test_unauthenticated_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(401);
    }
}
