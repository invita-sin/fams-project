<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\AttendanceSchedule;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Employee $employee;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('super_admin');

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $location = WorkLocation::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meter' => 100,
            'active' => true,
        ]);

        $schedule = AttendanceSchedule::factory()->create([
            'checkin_start' => '00:00:00',
            'checkin_end' => '23:59:00',
            'checkout_time' => '23:59:00',
        ]);

        $this->employee->schedules()->attach($schedule->id, [
            'id' => (string) str()->uuid(),
            'effective_date' => today()->subDay(),
        ]);

        Attendance::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);

        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    public function test_can_export_attendance_xlsx(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/attendance/export?format=xlsx&start_date=' . today()->subMonth()->toDateString() . '&end_date=' . today()->toDateString());

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_export_attendance_csv(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/attendance/export?format=csv&start_date=' . today()->subMonth()->toDateString() . '&end_date=' . today()->toDateString());

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_can_export_documentations_xlsx(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/documentations/export?format=xlsx');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_export_attendance_pdf(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/attendance/export?format=pdf&start_date=' . today()->subMonth()->toDateString() . '&end_date=' . today()->toDateString());

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_can_export_documentations_pdf(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/documentations/export?format=pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unauthenticated_cannot_export_reports(): void
    {
        $response = $this->getJson('/api/v1/reports/attendance/export');
        $response->assertStatus(401);
    }
}
