<?php

namespace App\DTOs;

class UpdateScheduleData
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $checkinStart,
        public readonly ?string $checkinEnd,
        public readonly ?string $checkoutTime,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            checkinStart: $data['checkin_start'] ?? null,
            checkinEnd: $data['checkin_end'] ?? null,
            checkoutTime: $data['checkout_time'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'checkin_start' => $this->checkinStart,
            'checkin_end' => $this->checkinEnd,
            'checkout_time' => $this->checkoutTime,
        ], fn ($value) => $value !== null);
    }
}
