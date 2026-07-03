<?php

namespace Database\Factories;

use App\Models\DocumentationPhoto;
use App\Models\FieldDocumentation;
use App\Models\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentationPhotoFactory extends Factory
{
    protected $model = DocumentationPhoto::class;

    public function definition(): array
    {
        return [
            'documentation_id' => FieldDocumentation::factory(),
            'media_file_id' => MediaFile::factory(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
