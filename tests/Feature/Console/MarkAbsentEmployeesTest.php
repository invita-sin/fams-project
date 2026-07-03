<?php

namespace Tests\Feature\Console;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkAbsentEmployeesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_marks_employees_without_attendance_as_absent(): void
    {
        $user1 = User::factory()->create();
        $user1->assignRole('employee');
        $employee1 = Employee::factory()->create([
            'user_id' => $user1->id,
            'status' => 'active',
        ]);
        Attendance::factory()->create([
            'employee_id' => $employee1->id,
            'attendance_date' => today(),
            'status' => 'present',
        ]);

        $user2 = User::factory()->create();
        $user2->assignRole('employee');
        $employee2 = Employee::factory()->create([
            'user_id' => $user2->id,
            'status' => 'active',
        ]);

        $this->artisan('attendance:summary')
            ->assertSuccessful();

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee2->id,
            'attendance_date' => today()->toDateString(),
            'status' => 'absent',
        ]);
    }

    public function test_does_not_mark_inactive_employees(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');
        $inactiveEmployee = Employee::factory()->create([
            'user_id' => $user->id,
            'status' => 'inactive',
        ]);

        $this->artisan('attendance:summary')
            ->assertSuccessful();

        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $inactiveEmployee->id,
            'attendance_date' => today()->toDateString(),
        ]);
    }

    public function test_skips_employees_with_existing_attendance(): void
    {
        $user = User::factory()->create();
        $user->assignRole('employee');
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'attendance_date' => today(),
            'status' => 'present',
        ]);

        $this->artisan('attendance:summary')
            ->assertSuccessful();

        $this->assertEquals(1, Attendance::where('employee_id', $employee->id)
            ->where('attendance_date', today())->count());
    }
}
