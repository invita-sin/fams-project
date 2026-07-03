<?php

namespace App\Repositories;

use App\Models\AttendanceCorrection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttendanceCorrectionRepositoryInterface
{
    public function findById(string $id): ?AttendanceCorrection;

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function create(array $data): AttendanceCorrection;

    public function update(AttendanceCorrection $correction, array $data): AttendanceCorrection;
}
