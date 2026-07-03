<?php

namespace App\DTOs;

class CreateWorkLocationData
{
    public function __construct(
        public readonly string $name,
        public readonly string $latitude,
        public readonly string $longitude,
        public readonly ?string $branchId,
        public readonly ?string $address,
        public readonly int $radiusMeter = 100,
        public readonly bool $active = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            latitude: $data['latitude'],
            longitude: $data['longitude'],
            branchId: $data['branch_id'] ?? null,
            address: $data['address'] ?? null,
            radiusMeter: (int) ($data['radius_meter'] ?? 100),
            active: (bool) ($data['active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'branch_id' => $this->branchId,
            'address' => $this->address,
            'radius_meter' => $this->radiusMeter,
            'active' => $this->active,
        ];
    }
}
