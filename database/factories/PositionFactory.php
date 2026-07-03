<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->unique()->randomElement([
                'Junior Engineer',
                'Senior Engineer',
                'Tech Lead',
                'Engineering Manager',
                'QA Tester',
                'Product Designer',
                'HR Specialist',
                'Accountant',
                'Marketing Lead',
                'Operations Manager',
            ]),
        ];
    }
}
