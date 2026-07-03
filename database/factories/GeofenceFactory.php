<?php

namespace Database\Factories;

use App\Models\Geofence;
use App\Models\WorkLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeofenceFactory extends Factory
{
    protected $model = Geofence::class;

    public function definition(): array
    {
        return [
            'work_location_id' => WorkLocation::factory(),
            'active' => true,
        ];
    }
}
