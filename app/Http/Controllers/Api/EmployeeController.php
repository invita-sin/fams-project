<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateEmployeeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeImportService;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService,
    ) {}

    public function import(Request $request, EmployeeImportService $importService): JsonResponse
    {
        $this->authorize('create', Employee::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $result = $importService->import($request->file('file'));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$result['imported']} dari {$result['total']} data",
            'data' => $result,
        ]);
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Employee::class);

        $filters = request()->only(['search', 'division', 'status']);
        $employees = $this->employeeService->paginate(10, $filters);

        return response()->json([
            'success' => true,
            'data' => $employees->items(),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
        ]);
    }

    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        $this->authorize('create', Employee::class);

        $data = CreateEmployeeData::fromRequest($request->validated());
        $employee = $this->employeeService->createEmployee($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee created',
            'data' => $employee,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $employee = $this->employeeService->getEmployee($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $this->authorize('view', $employee);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    public function update(CreateEmployeeRequest $request, string $id): JsonResponse
    {
        $employee = $this->employeeService->getEmployee($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $this->authorize('update', $employee);

        $data = CreateEmployeeData::fromRequest($request->validated());

        try {
            $employee = $this->employeeService->updateEmployee($id, $data);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee updated',
            'data' => $employee,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = $this->employeeService->getEmployee($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $this->authorize('delete', $employee);

        try {
            $this->employeeService->deactivateEmployee($id);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee deactivated',
        ]);
    }
}
