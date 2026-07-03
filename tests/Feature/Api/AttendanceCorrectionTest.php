<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkLocation;
use App\Notifications\AttendanceCorrectionApproved;
use App\Notifications\AttendanceCorrectionRejected;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private User $employeeUser;
    private User $supervisorUser;
    private Employee $employee;
    private Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->employeeUser = User::factory()->create(['email' => 'emp@fams.test']);
        $this->employeeUser->assignRole('employee');

        $this->supervisorUser = User::factory()->create(['email' => 'sup@fams.test']);
        $this->supervisorUser->assignRole('supervisor');

        $supervisor = Employee::factory()->create([
            'user_id' => $this->supervisorUser->id,
            'status' => 'active',
        ]);

        $this->employee = Employee::factory()->create([
            'user_id' => $this->employeeUser->id,
            'supervisor_id' => $supervisor->id,
            'status' => 'active',
        ]);

        WorkLocation::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meter' => 100,
            'active' => true,
        ]);

        $this->attendance = Attendance::factory()->create([
            'employee_id' => $this->employee->id,
            'attendance_date' => today(),
            'checkin_time' => now(),
            'status' => 'present',
        ]);
    }

    public function test_can_create_correction(): void
    {
        $token = $this->supervisorUser->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/attendance-corrections', [
                'attendance_id' => $this->attendance->id,
                'reason' => 'I forgot to check out because I had an emergency meeting.',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendance_corrections', [
            'attendance_id' => $this->attendance->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_list_corrections(): void
    {
        AttendanceCorrection::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);

        $token = $this->supervisorUser->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/attendance-corrections');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_can_show_correction(): void
    {
        $correction = AttendanceCorrection::factory()->create([
            'employee_id' => $this->employee->id,
            'attendance_id' => $this->attendance->id,
        ]);

        $token = $this->supervisorUser->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/attendance-corrections/{$correction->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_supervisor_can_approve_correction(): void
    {
        Notification::fake();

        $correction = AttendanceCorrection::factory()->create([
            'employee_id' => $this->employee->id,
            'attendance_id' => $this->attendance->id,
            'status' => 'pending',
        ]);

        $token = $this->supervisorUser->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/attendance-corrections/{$correction->id}/approve");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => 'approved',
            'reviewed_by' => $this->supervisorUser->id,
        ]);

        Notification::assertSentTo(
            $this->employeeUser,
            AttendanceCorrectionApproved::class,
        );
    }

    public function test_supervisor_can_reject_correction(): void
    {
        Notification::fake();

        $correction = AttendanceCorrection::factory()->create([
            'employee_id' => $this->employee->id,
            'attendance_id' => $this->attendance->id,
            'status' => 'pending',
        ]);

        $token = $this->supervisorUser->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(
                "/api/v1/attendance-corrections/{$correction->id}/reject",
                ['reason' => 'Insufficient evidence provided']
            );

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => 'rejected',
        ]);

        Notification::assertSentTo(
            $this->employeeUser,
            AttendanceCorrectionRejected::class,
        );
    }

    public function test_cannot_approve_already_processed_correction(): void
    {
        $correction = AttendanceCorrection::factory()->create([
            'employee_id' => $this->employee->id,
            'attendance_id' => $this->attendance->id,
            'status' => 'approved',
        ]);

        $token = $this->supervisorUser->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/attendance-corrections/{$correction->id}/approve");

        $response->assertStatus(422);
    }

    public function test_unauthenticated_cannot_access_corrections(): void
    {
        $response = $this->postJson('/api/v1/attendance-corrections', [
            'attendance_id' => $this->attendance->id,
            'reason' => 'Test reason.',
        ]);

        $response->assertStatus(401);
    }
}
