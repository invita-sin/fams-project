<?php

namespace App\Repositories;

use App\Models\AttendanceCorrection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceCorrectionRepository implements AttendanceCorrectionRepositoryInterface
{
    public function findById(string $id): ?AttendanceCorrection
    {
        return AttendanceCorrection::with(['attendance', 'employee.user', 'reviewer'])->find($id);
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = AttendanceCorrection::with(['attendance', 'employee.user', 'reviewer']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): AttendanceCorrection
    {
        return AttendanceCorrection::create($data);
    }

    public function update(AttendanceCorrection $correction, array $data): AttendanceCorrection
    {
        $correction->update($data);
        return $correction->fresh();
    }
}
