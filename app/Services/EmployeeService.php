<?php

namespace App\Services;

use App\DTOs\CreateEmployeeData;
use App\Models\Employee;
use App\Repositories\EmployeeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
    ) {}

    public function createEmployee(CreateEmployeeData $data): Employee
    {
        return $this->employeeRepository->create($data->toArray());
    }

    public function updateEmployee(string $id, CreateEmployeeData $data): Employee
    {
        $employee = $this->employeeRepository->findById($id);

        if (!$employee) {
            throw new \RuntimeException('Employee not found');
        }

        return $this->employeeRepository->update($employee, $data->toArray());
    }

    public function deactivateEmployee(string $id): Employee
    {
        $employee = $this->employeeRepository->findById($id);

        if (!$employee) {
            throw new \RuntimeException('Employee not found');
        }

        return $this->employeeRepository->deactivate($employee);
    }

    public function getEmployee(string $id): ?Employee
    {
        return $this->employeeRepository->findById($id);
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->employeeRepository->paginate($perPage, $filters);
    }
}
