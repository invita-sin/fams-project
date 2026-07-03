<?php

namespace App\DTOs;

class AssignScheduleData
{
    public function __construct(
        public readonly string $employeeId,
        public readonly string $scheduleId,
        public readonly string $effectiveDate,
        public readonly ?string $endDate,
    ) {}

    public static function fromRequest(array $data, string $employeeId): self
    {
        return new self(
            employeeId: $employeeId,
            scheduleId: $data['schedule_id'],
            effectiveDate: $data['effective_date'] ?? today()->toDateString(),
            endDate: $data['end_date'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'schedule_id' => $this->scheduleId,
            'effective_date' => $this->effectiveDate,
            'end_date' => $this->endDate,
        ];
    }
}
