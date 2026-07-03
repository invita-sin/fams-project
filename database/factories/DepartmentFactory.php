<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'division_id' => Division::factory(),
            'name' => fake()->unique()->randomElement([
                'Software Engineering',
                'Quality Assurance',
                'UI/UX Design',
                'Human Resources',
                'Finance',
                'Marketing',
                'Operations',
                'Sales',
                'Legal',
                'Customer Support',
            ]),
        ];
    }
}
