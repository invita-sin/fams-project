<?php

namespace Tests\Feature\Filament;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_access_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@fams.test',
            'name' => 'Admin',
        ])->assignRole('super_admin');

        $user = User::where('email', 'admin@fams.test')->first();

        $response = $this->actingAs($user, 'web')
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_dashboard_returns_successful(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
        ])->assignRole('super_admin');

        Employee::factory()->count(3)->create();

        $response = $this->actingAs($user, 'web')
            ->get('/admin');

        $response->assertStatus(200);
    }
}
