<?php

namespace App\Repositories;

use App\Models\AttendanceSchedule;

interface ScheduleRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function findById(string $id): ?AttendanceSchedule;

    public function create(array $data): AttendanceSchedule;

    public function update(AttendanceSchedule $schedule, array $data): AttendanceSchedule;

    public function delete(AttendanceSchedule $schedule): bool;
}
