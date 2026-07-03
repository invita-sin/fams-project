<?php

namespace Tests\Feature\Api;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->token = $user->createToken('test-token')->plainTextToken;
    }

    public function test_admin_dashboard_returns_stats(): void
    {
        Employee::factory()->create(['status' => 'active']);
        Employee::factory()->create(['status' => 'inactive']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/dashboard/admin');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_employees',
                    'active_employees',
                    'today_attendance',
                    'pending_corrections',
                    'total_documentations',
                    'today_documentations',
                ],
            ])
            ->assertJsonPath('data.total_employees', 2)
            ->assertJsonPath('data.active_employees', 1);
    }

    public function test_unauthenticated_cannot_access_dashboard(): void
    {
        $response = $this->getJson('/api/v1/dashboard/admin');

        $response->assertStatus(401);
    }
}
