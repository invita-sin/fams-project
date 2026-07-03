<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\WorkLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkLocationFactory extends Factory
{
    protected $model = WorkLocation::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'name' => fake()->word(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'radius_meter' => 100,
            'active' => true,
        ];
    }
}
