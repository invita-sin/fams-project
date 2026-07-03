<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_dashboard(): void
    {
        $role = Role::create(['name' => 'employee']);
        $user = User::factory()->create();
        $user->assignRole('employee');

        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->employee_id = $employee->id;
        $user->save();

        $response = $this->actingAs($user)
            ->get('/admin/employee-dashboard');

        $response->assertStatus(200);
    }

    public function test_non_employee_cannot_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/admin/employee-dashboard');

        $response->assertStatus(403);
    }
}
