<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Support\Collection;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function update(Attendance $attendance, array $data): Attendance
    {
        $attendance->update($data);
        return $attendance->fresh();
    }

    public function findToday(string $employeeId): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereDate('attendance_date', today())
            ->first();
    }

    public function getHistory(string $employeeId, array $filters = []): Collection
    {
        $query = Attendance::with(['logs', 'photos.mediaFile'])
            ->where('employee_id', $employeeId);

        if (!empty($filters['start_date'])) {
            $query->whereDate('attendance_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('attendance_date', '<=', $filters['end_date']);
        }

        return $query->orderBy('attendance_date', 'desc')->get();
    }
}
