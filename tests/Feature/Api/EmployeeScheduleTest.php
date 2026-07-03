<?php

namespace Tests\Feature\Api;

use App\Models\AttendanceSchedule;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkLocation;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Employee $employee;
    private AttendanceSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create(['name' => 'Admin']);
        $this->user->assignRole('super_admin');

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $this->schedule = AttendanceSchedule::factory()->create([
            'name' => 'Morning Shift',
            'checkin_start' => '00:00:00',
            'checkin_end' => '23:59:00',
            'checkout_time' => '23:59:00',
        ]);
    }

    public function test_can_assign_schedule_to_employee(): void
    {
        $token = $this->user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/employees/{$this->employee->id}/schedules", [
                'schedule_id' => $this->schedule->id,
                'effective_date' => today()->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendance_schedule_employee', [
            'employee_id' => $this->employee->id,
            'schedule_id' => $this->schedule->id,
        ]);
    }

    public function test_can_list_employee_schedules(): void
    {
        $this->employee->schedules()->attach($this->schedule->id, [
            'id' => (string) str()->uuid(),
            'effective_date' => today(),
        ]);

        $token = $this->user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/employees/{$this->employee->id}/schedules");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_get_active_schedule(): void
    {
        $this->employee->schedules()->attach($this->schedule->id, [
            'id' => (string) str()->uuid(),
            'effective_date' => today()->subDay(),
        ]);

        $token = $this->user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/employees/{$this->employee->id}/schedules/active");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Morning Shift',
                ],
            ]);
    }

    public function test_returns_404_when_no_active_schedule(): void
    {
        $token = $this->user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/employees/{$this->employee->id}/schedules/active");

        $response->assertStatus(404);
    }

    public function test_can_remove_schedule_from_employee(): void
    {
        $this->employee->schedules()->attach($this->schedule->id, [
            'id' => (string) str()->uuid(),
            'effective_date' => today(),
        ]);

        $token = $this->user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/employees/{$this->employee->id}/schedules/{$this->schedule->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('attendance_schedule_employee', [
            'employee_id' => $this->employee->id,
            'schedule_id' => $this->schedule->id,
        ]);
    }

    public function test_checkin_requires_active_schedule(): void
    {
        $this->user->assignRole('employee');

        WorkLocation::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meter' => 100,
            'active' => true,
        ]);

        $token = $this->user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/attendance/checkin', [
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'accuracy' => 10,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'No active schedule assigned for today',
            ]);
    }

    public function test_checkin_succeeds_with_active_schedule(): void
    {
        $this->user->assignRole('employee');

        WorkLocation::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meter' => 100,
            'active' => true,
        ]);

        $this->employee->schedules()->attach($this->schedule->id, [
            'id' => (string) str()->uuid(),
            'effective_date' => today()->subDay(),
        ]);

        $token = $this->user->createToken('api-token')->plainTextToken;
        $photo = \Illuminate\Http\UploadedFile::fake()->image('selfie.jpg', 300, 300);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/v1/attendance/checkin', [
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'accuracy' => 10,
                'photo' => $photo,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_cannot_access_schedule_assignment(): void
    {
        $response = $this->postJson("/api/v1/employees/{$this->employee->id}/schedules", [
            'schedule_id' => $this->schedule->id,
        ]);

        $response->assertStatus(401);
    }
}
