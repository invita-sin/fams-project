<?php

namespace App\DTOs;

class UpdateWorkLocationData
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $latitude,
        public readonly ?string $longitude,
        public readonly ?string $branchId,
        public readonly ?string $address,
        public readonly ?int $radiusMeter,
        public readonly ?bool $active,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            branchId: $data['branch_id'] ?? null,
            address: $data['address'] ?? null,
            radiusMeter: isset($data['radius_meter']) ? (int) $data['radius_meter'] : null,
            active: isset($data['active']) ? (bool) $data['active'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'branch_id' => $this->branchId,
            'address' => $this->address,
            'radius_meter' => $this->radiusMeter,
            'active' => $this->active,
        ], fn ($value) => $value !== null);
    }
}
