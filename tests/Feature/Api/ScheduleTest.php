<?php

namespace Tests\Feature\Api;

use App\Models\AttendanceSchedule;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
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

    public function test_can_create_schedule(): void
    {
        $response = $this->postJson('/api/v1/schedules', [
            'name' => 'Morning Shift',
            'checkin_start' => '07:00:00',
            'checkin_end' => '08:00:00',
            'checkout_time' => '16:00:00',
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Schedule created',
            ]);

        $this->assertDatabaseHas('attendance_schedules', [
            'name' => 'Morning Shift',
        ]);
    }

    public function test_can_list_schedules(): void
    {
        AttendanceSchedule::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/schedules', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_show_schedule(): void
    {
        $schedule = AttendanceSchedule::factory()->create([
            'name' => 'Night Shift',
        ]);

        $response = $this->getJson('/api/v1/schedules/' . $schedule->id, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Night Shift',
                ],
            ]);
    }

    public function test_can_update_schedule(): void
    {
        $schedule = AttendanceSchedule::factory()->create();

        $response = $this->putJson('/api/v1/schedules/' . $schedule->id, [
            'name' => 'Updated Shift',
        ], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Schedule updated',
            ]);

        $this->assertDatabaseHas('attendance_schedules', [
            'id' => $schedule->id,
            'name' => 'Updated Shift',
        ]);
    }

    public function test_can_delete_schedule(): void
    {
        $schedule = AttendanceSchedule::factory()->create();

        $response = $this->deleteJson('/api/v1/schedules/' . $schedule->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Schedule deleted',
            ]);

        $this->assertDatabaseMissing('attendance_schedules', [
            'id' => $schedule->id,
        ]);
    }

    public function test_unauthenticated_cannot_access_schedules(): void
    {
        $response = $this->getJson('/api/v1/schedules');

        $response->assertStatus(401);
    }
}
