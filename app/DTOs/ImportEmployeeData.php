<?php

namespace App\DTOs;

class ImportEmployeeData
{
    public function __construct(
        public readonly ?string $employeeCode,
        public readonly string $fullName,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $divisionId,
        public readonly ?string $positionId,
        public readonly ?string $status,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'employee_code' => $this->employeeCode,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'division_id' => $this->divisionId,
            'position_id' => $this->positionId,
            'status' => $this->status ?? 'active',
        ], fn ($v) => $v !== null);
    }
}
