<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceLogFactory extends Factory
{
    protected $model = AttendanceLog::class;

    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'event_type' => fake()->randomElement(['checkin', 'checkout']),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'accuracy' => fake()->randomFloat(2, 1, 50),
            'distance_meter' => fake()->randomFloat(2, 0, 500),
            'device_info' => ['device' => fake()->userAgent(), 'platform' => 'mobile'],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
