<?php

namespace App\DTOs;

class CheckOutData
{
    public function __construct(
        public readonly string $employeeId,
        public readonly string $attendanceId,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly float $accuracy,
        public readonly ?string $notes,
        public readonly mixed $photo,
    ) {}

    public static function fromRequest(array $data, string $employeeId, string $attendanceId): self
    {
        return new self(
            employeeId: $employeeId,
            attendanceId: $attendanceId,
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            accuracy: (float) ($data['accuracy'] ?? 0),
            notes: $data['notes'] ?? null,
            photo: $data['photo'] ?? null,
        );
    }
}
