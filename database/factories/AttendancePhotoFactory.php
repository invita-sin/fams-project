<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\AttendancePhoto;
use App\Models\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendancePhotoFactory extends Factory
{
    protected $model = AttendancePhoto::class;

    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'photo_type' => fake()->randomElement(['checkin', 'checkout']),
            'media_file_id' => MediaFile::factory(),
        ];
    }
}
