<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\AttendanceSchedule;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkLocation;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Employee $employee;
    private WorkLocation $location;
    private AttendanceSchedule $schedule;
    private string $token;
    private UploadedFile $photo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::factory()->create([
            'email' => 'emp@fams.test',
        ]);
        $this->user->assignRole('employee');

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $this->location = WorkLocation::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meter' => 100,
            'active' => true,
        ]);

        $this->schedule = AttendanceSchedule::factory()->create([
            'checkin_start' => '00:00:00',
            'checkin_end' => '23:59:00',
            'checkout_time' => '23:59:00',
        ]);

        $this->employee->schedules()->attach($this->schedule->id, [
            'id' => (string) str()->uuid(),
            'effective_date' => today()->subDay(),
        ]);

        $this->token = $this->user->createToken('api-token')->plainTextToken;
        $this->photo = UploadedFile::fake()->image('selfie.jpg', 300, 300);
    }

    public function test_can_check_in(): void
    {
        $response = $this->withToken($this->token)->post('/api/v1/attendance/checkin', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
            'photo' => $this->photo,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_cannot_check_in_twice(): void
    {
        $this->withToken($this->token)->post('/api/v1/attendance/checkin', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
            'photo' => $this->photo,
        ]);

        $response = $this->withToken($this->token)->post('/api/v1/attendance/checkin', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
            'photo' => $this->photo,
        ]);

        $response->assertStatus(422);
    }

    public function test_can_check_out(): void
    {
        $this->withToken($this->token)->post('/api/v1/attendance/checkin', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
            'photo' => $this->photo,
        ]);

        $response = $this->withToken($this->token)->post('/api/v1/attendance/checkout', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
            'photo' => $this->photo,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_cannot_check_out_without_check_in(): void
    {
        $response = $this->withToken($this->token)->post('/api/v1/attendance/checkout', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
            'photo' => $this->photo,
        ]);

        $response->assertStatus(404);
    }

    public function test_can_get_attendance_history(): void
    {
        Attendance::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/attendance/history');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_cannot_access_attendance(): void
    {
        $response = $this->postJson('/api/v1/attendance/checkin', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
        ]);

        $response->assertStatus(401);
    }

    public function test_checkin_requires_photo(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/attendance/checkin', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'accuracy' => 10,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Foto selfie wajib diambil',
            ]);
    }
}
