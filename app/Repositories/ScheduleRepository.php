<?php

namespace App\Repositories;

use App\Models\AttendanceSchedule;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return AttendanceSchedule::orderBy('name')->get();
    }

    public function findById(string $id): ?AttendanceSchedule
    {
        return AttendanceSchedule::find($id);
    }

    public function create(array $data): AttendanceSchedule
    {
        return AttendanceSchedule::create($data);
    }

    public function update(AttendanceSchedule $schedule, array $data): AttendanceSchedule
    {
        $schedule->update($data);

        return $schedule->fresh();
    }

    public function delete(AttendanceSchedule $schedule): bool
    {
        return $schedule->delete();
    }
}
