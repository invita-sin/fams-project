<?php

namespace Database\Factories;

use App\Models\AttendanceSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceScheduleFactory extends Factory
{
    protected $model = AttendanceSchedule::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Shift',
            'checkin_start' => '07:00:00',
            'checkin_end' => '08:00:00',
            'checkout_time' => '16:00:00',
        ];
    }
}
