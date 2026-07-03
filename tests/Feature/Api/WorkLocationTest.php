<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\User;
use App\Models\WorkLocation;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkLocationTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $user = User::factory()->create(['name' => 'Admin']);
        $user->assignRole('super_admin');
        $this->token = $user->createToken('test')->plainTextToken;
    }

    public function test_can_create_work_location(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->postJson('/api/v1/work-locations', [
            'name' => 'Kantor Pusat',
            'latitude' => '-6.2088',
            'longitude' => '106.8456',
            'branch_id' => $branch->id,
            'address' => 'Jl. Sudirman No. 1',
            'radius_meter' => 200,
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Work location created',
            ]);

        $this->assertDatabaseHas('work_locations', [
            'name' => 'Kantor Pusat',
        ]);
    }

    public function test_can_list_work_locations(): void
    {
        WorkLocation::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/work-locations', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_show_work_location(): void
    {
        $location = WorkLocation::factory()->create([
            'name' => 'Cabang Jakarta',
        ]);

        $response = $this->getJson('/api/v1/work-locations/' . $location->id, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Cabang Jakarta',
                ],
            ]);
    }

    public function test_can_update_work_location(): void
    {
        $location = WorkLocation::factory()->create();

        $response = $this->putJson('/api/v1/work-locations/' . $location->id, [
            'name' => 'Cabang Bandung',
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Work location updated',
            ]);

        $this->assertDatabaseHas('work_locations', [
            'id' => $location->id,
            'name' => 'Cabang Bandung',
        ]);
    }

    public function test_can_delete_work_location(): void
    {
        $location = WorkLocation::factory()->create();

        $response = $this->deleteJson('/api/v1/work-locations/' . $location->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Work location deleted',
            ]);

        $this->assertDatabaseMissing('work_locations', [
            'id' => $location->id,
        ]);
    }

    public function test_unauthenticated_cannot_access_work_locations(): void
    {
        $response = $this->getJson('/api/v1/work-locations');

        $response->assertStatus(401);
    }

    public function test_cannot_create_with_invalid_coordinates(): void
    {
        $response = $this->postJson('/api/v1/work-locations', [
            'name' => 'Invalid',
            'latitude' => '100',
            'longitude' => '200',
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(422);
    }
}
