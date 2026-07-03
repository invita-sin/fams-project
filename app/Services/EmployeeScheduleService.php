<?php

namespace App\Services;

use App\DTOs\AssignScheduleData;
use App\Exceptions\Domain\ResourceNotFoundException;
use App\Models\AttendanceSchedule;
use App\Models\Employee;
use App\Repositories\EmployeeRepositoryInterface;
use App\Repositories\ScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeScheduleService
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly ScheduleRepositoryInterface $scheduleRepository,
    ) {}

    public function assignSchedule(AssignScheduleData $data): void
    {
        $employee = $this->employeeRepository->findById($data->employeeId);

        if (!$employee) {
            throw new \RuntimeException('Employee not found');
        }

        $schedule = $this->scheduleRepository->findById($data->scheduleId);

        if (!$schedule) {
            throw new ResourceNotFoundException('Schedule not found');
        }

        $employee->schedules()->attach($data->scheduleId, [
            'id' => (string) str()->uuid(),
            'effective_date' => $data->effectiveDate,
            'end_date' => $data->endDate,
        ]);
    }

    public function removeSchedule(string $employeeId, string $scheduleId): void
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new \RuntimeException('Employee not found');
        }

        $detached = $employee->schedules()->detach($scheduleId);

        if (!$detached) {
            throw new \RuntimeException('Schedule assignment not found');
        }
    }

    public function getEmployeeSchedules(string $employeeId): Collection
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new \RuntimeException('Employee not found');
        }

        return $employee->schedules()->withPivot(['effective_date', 'end_date'])->get();
    }

    public function getActiveSchedule(string $employeeId): ?AttendanceSchedule
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            return null;
        }

        return $employee->activeSchedule();
    }
}
