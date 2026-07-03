<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_code' => fake()->unique()->bothify('EMP-####'),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'status' => 'active',
        ];
    }
}
