<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AttendanceRepositoryInterface
{
    public function create(array $data): Attendance;

    public function update(Attendance $attendance, array $data): Attendance;

    public function findToday(string $employeeId): ?Attendance;

    public function getHistory(string $employeeId, array $filters = []): Collection;
}
