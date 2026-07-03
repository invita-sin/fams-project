<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeRepositoryInterface
{
    public function findById(string $id): ?Employee;

    public function findByCode(string $code): ?Employee;

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function create(array $data): Employee;

    public function update(Employee $employee, array $data): Employee;

    public function deactivate(Employee $employee): Employee;
}
