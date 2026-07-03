<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionFactory extends Factory
{
    protected $model = AttendanceCorrection::class;

    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'employee_id' => Employee::factory(),
            'reason' => fake()->paragraph(),
            'status' => 'pending',
        ];
    }
}
