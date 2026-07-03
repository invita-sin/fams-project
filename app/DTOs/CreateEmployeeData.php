<?php

namespace App\DTOs;

class CreateEmployeeData
{
    public function __construct(
        public readonly string $employeeCode,
        public readonly string $fullName,
        public readonly ?string $phone,
        public readonly ?string $gender,
        public readonly ?string $branchId,
        public readonly ?string $divisionId,
        public readonly ?string $departmentId,
        public readonly ?string $positionId,
        public readonly ?string $hireDate,
        public readonly string $status = 'active',
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            employeeCode: $data['employee_code'],
            fullName: $data['full_name'],
            phone: $data['phone'] ?? null,
            gender: $data['gender'] ?? null,
            branchId: $data['branch_id'] ?? null,
            divisionId: $data['division_id'] ?? null,
            departmentId: $data['department_id'] ?? null,
            positionId: $data['position_id'] ?? null,
            hireDate: $data['hire_date'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'employee_code' => $this->employeeCode,
            'full_name' => $this->fullName,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'branch_id' => $this->branchId,
            'division_id' => $this->divisionId,
            'department_id' => $this->departmentId,
            'position_id' => $this->positionId,
            'hire_date' => $this->hireDate,
            'status' => $this->status,
        ];
    }
}
