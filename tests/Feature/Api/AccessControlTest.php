<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function assertStatus(string $email, string $method, string $uri, int $expected, array $data = []): void
    {
        $user = User::where('email', $email)->first();
        $token = $user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $response = $method === 'GET'
            ? $this->getJson($uri, $headers)
            : $this->postJson($uri, $data, $headers);

        $this->assertEquals($expected, $response->getStatusCode(), "$email @ $method $uri");
    }

    // ========== NEW DASHBOARDS ==========

    public function test_employee_dashboard(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/dashboard/employee', 200);
    }

    public function test_employee_blocked_supervisor_dashboard(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/dashboard/supervisor', 403);
    }

    public function test_supervisor_blocked_employee_dashboard(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/dashboard/employee', 403);
    }

    public function test_supervisor_dashboard(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/dashboard/supervisor', 200);
    }

    public function test_admin_dashboard(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/dashboard/admin', 200);
    }

    public function test_admin_cannot_employee_dashboard(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/dashboard/employee', 403);
    }

    public function test_admin_cannot_supervisor_dashboard(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/dashboard/supervisor', 403);
    }

    // ========== CHECKIN ACCESS ==========

    public function test_employee_can_checkin(): void
    {
        $this->assertStatus('employee1@fams.test', 'POST', '/api/v1/attendance/checkin', 422, [
            'latitude' => -6.2088, 'longitude' => 106.8456, 'accuracy' => 10,
        ]);
    }

    public function test_supervisor_can_checkin(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'POST', '/api/v1/attendance/checkin', 422, [
            'latitude' => -6.2088, 'longitude' => 106.8456, 'accuracy' => 10,
        ]);
    }

    // ========== EMPLOYEE ==========

    public function test_employee_attendance_history(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/attendance/history', 200);
    }

    public function test_employee_blocked_admin_dashboard(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/dashboard/admin', 403);
    }

    public function test_employee_blocked_employees(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/employees', 403);
    }

    public function test_employee_blocked_documentations(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/documentations', 403);
    }

    public function test_employee_blocked_corrections(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/attendance-corrections', 403);
    }

    public function test_employee_blocked_schedules(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/schedules', 403);
    }

    public function test_employee_blocked_work_locations(): void
    {
        $this->assertStatus('employee1@fams.test', 'GET', '/api/v1/work-locations', 403);
    }

    // ========== SUPERVISOR ==========

    public function test_supervisor_can_admin_dashboard(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/dashboard/admin', 200);
    }

    public function test_supervisor_can_employees(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/employees', 200);
    }

    public function test_supervisor_can_documentations(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/documentations', 200);
    }

    public function test_supervisor_can_corrections(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/attendance-corrections', 200);
    }

    public function test_supervisor_can_schedules(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/schedules', 200);
    }

    public function test_supervisor_can_work_locations(): void
    {
        $this->assertStatus('supervisor1@fams.test', 'GET', '/api/v1/work-locations', 200);
    }

    // ========== ADMIN ==========

    public function test_admin_can_admin_dashboard(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/dashboard/admin', 200);
    }

    public function test_admin_can_employees(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/employees', 200);
    }

    public function test_admin_can_documentations(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/documentations', 200);
    }

    public function test_admin_can_corrections(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/attendance-corrections', 200);
    }

    public function test_admin_can_schedules(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/schedules', 200);
    }

    public function test_admin_can_work_locations(): void
    {
        $this->assertStatus('admin@fams.test', 'GET', '/api/v1/work-locations', 200);
    }
}
