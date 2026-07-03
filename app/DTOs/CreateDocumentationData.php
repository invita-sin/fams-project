<?php

namespace App\DTOs;

class CreateDocumentationData
{
    public function __construct(
        public readonly string $employeeId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $category,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly string $activityDate,
    ) {}

    public static function fromRequest(array $data, string $employeeId): self
    {
        return new self(
            employeeId: $employeeId,
            title: $data['title'],
            description: $data['description'] ?? null,
            category: $data['category'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            activityDate: $data['activity_date'] ?? today()->toDateString(),
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'activity_date' => $this->activityDate,
        ];
    }
}
