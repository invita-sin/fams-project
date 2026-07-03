<?php

namespace App\Http\Controllers\Api;

use App\DTOs\AssignScheduleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignScheduleRequest;
use App\Models\AttendanceSchedule;
use App\Models\Employee;
use App\Services\EmployeeScheduleService;
use Illuminate\Http\JsonResponse;

class EmployeeScheduleController extends Controller
{
    public function __construct(
        private readonly EmployeeScheduleService $employeeScheduleService,
    ) {}

    public function schedules(string $employeeId): JsonResponse
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $this->authorize('view', $employee);

        try {
            $schedules = $this->employeeScheduleService->getEmployeeSchedules($employeeId);

            return response()->json([
                'success' => true,
                'data' => $schedules,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function activeSchedule(string $employeeId): JsonResponse
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $this->authorize('view', $employee);

        $schedule = $this->employeeScheduleService->getActiveSchedule($employeeId);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'No active schedule found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    public function assign(AssignScheduleRequest $request, string $employeeId): JsonResponse
    {
        $this->authorize('update', AttendanceSchedule::class);

        try {
            $data = AssignScheduleData::fromRequest($request->validated(), $employeeId);
            $this->employeeScheduleService->assignSchedule($data);

            return response()->json([
                'success' => true,
                'message' => 'Schedule assigned',
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function remove(string $employeeId, string $scheduleId): JsonResponse
    {
        $this->authorize('update', AttendanceSchedule::class);

        try {
            $this->employeeScheduleService->removeSchedule($employeeId, $scheduleId);

            return response()->json([
                'success' => true,
                'message' => 'Schedule removed',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
