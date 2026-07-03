<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\FieldDocumentation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Employee $employee;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::factory()->create(['email' => 'admin@report.test']);
        $this->user->assignRole('super_admin');

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    public function test_can_get_attendance_report(): void
    {
        Attendance::factory()->count(5)->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/attendance?start_date=' . now()->subMonth()->toDateString() . '&end_date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['employee_id', 'employee_name', 'total_present', 'total_days', 'records'],
                ],
            ]);
    }

    public function test_can_get_documentation_report(): void
    {
        FieldDocumentation::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/documentations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['employee_id', 'employee_name', 'total_documentations', 'records'],
                ],
            ]);
    }

    public function test_can_get_employee_activity_report(): void
    {
        Attendance::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);

        FieldDocumentation::factory()->count(2)->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/reports/employee-activity');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['employee_id', 'employee_name', 'total_attendances', 'total_documentations'],
                ],
            ]);
    }

    public function test_unauthenticated_cannot_access_reports(): void
    {
        $response = $this->getJson('/api/v1/reports/attendance');

        $response->assertStatus(401);
    }
}
