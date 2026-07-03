<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\FieldDocumentation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldDocumentationFactory extends Factory
{
    protected $model = FieldDocumentation::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['visit', 'report', 'maintenance', 'delivery']),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'activity_date' => fake()->date(),
        ];
    }
}
