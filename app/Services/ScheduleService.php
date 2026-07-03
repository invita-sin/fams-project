<?php

namespace App\Services;

use App\DTOs\CreateScheduleData;
use App\DTOs\UpdateScheduleData;
use App\Models\AttendanceSchedule;
use App\Repositories\ScheduleRepositoryInterface;

class ScheduleService
{
    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository,
    ) {}

    public function getAllSchedules(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->scheduleRepository->all();
    }

    public function createSchedule(CreateScheduleData $data): AttendanceSchedule
    {
        return $this->scheduleRepository->create($data->toArray());
    }

    public function updateSchedule(string $id, UpdateScheduleData $data): AttendanceSchedule
    {
        $schedule = $this->scheduleRepository->findById($id);

        if (!$schedule) {
            throw new \RuntimeException('Schedule not found');
        }

        return $this->scheduleRepository->update($schedule, $data->toArray());
    }

    public function getSchedule(string $id): ?AttendanceSchedule
    {
        return $this->scheduleRepository->findById($id);
    }

    public function deleteSchedule(string $id): void
    {
        $schedule = $this->scheduleRepository->findById($id);

        if (!$schedule) {
            throw new \RuntimeException('Schedule not found');
        }

        $this->scheduleRepository->delete($schedule);
    }
}
