<?php

namespace Database\Factories;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFileFactory extends Factory
{
    protected $model = MediaFile::class;

    public function definition(): array
    {
        return [
            'folder_id' => MediaFolder::factory(),
            'file_name' => fake()->uuid() . '.jpg',
            'original_name' => fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(10000, 500000),
            'storage_disk' => 'local',
            'storage_path' => 'uploads/' . fake()->uuid() . '.jpg',
            'uploaded_by' => User::factory(),
        ];
    }
}
