<?php

namespace App\DTOs;

class CreateCorrectionData
{
    public function __construct(
        public readonly string $employeeId,
        public readonly string $attendanceId,
        public readonly string $reason,
    ) {}

    public static function fromRequest(array $data, string $employeeId): self
    {
        return new self(
            employeeId: $employeeId,
            attendanceId: $data['attendance_id'],
            reason: $data['reason'],
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'attendance_id' => $this->attendanceId,
            'reason' => $this->reason,
            'status' => 'pending',
        ];
    }
}
