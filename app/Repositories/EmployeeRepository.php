<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function findById(string $id): ?Employee
    {
        return Employee::with(['branch', 'division', 'department', 'position'])->find($id);
    }

    public function findByCode(string $code): ?Employee
    {
        return Employee::where('employee_code', $code)->first();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Employee::with(['branch', 'division', 'department', 'position']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', "%{$filters['search']}%")
                  ->orWhere('employee_code', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['division'])) {
            $query->where('division_id', $filters['division']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh();
    }

    public function deactivate(Employee $employee): Employee
    {
        $employee->update(['status' => 'inactive']);
        return $employee;
    }
}
