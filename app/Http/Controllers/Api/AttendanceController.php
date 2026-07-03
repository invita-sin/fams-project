<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CheckInData;
use App\DTOs\CheckOutData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        $this->authorize('create', Attendance::class);

        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found',
            ], 404);
        }

        $data = CheckInData::fromRequest($request->validated(), $employee->id);

        try {
            $attendance = $this->attendanceService->checkIn($data);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check in successful',
            'attendance_id' => $attendance->id,
        ]);
    }

    public function checkOut(CheckOutRequest $request): JsonResponse
    {
        $this->authorize('create', Attendance::class);

        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found',
            ], 404);
        }

        $todayAttendance = $this->attendanceService->getTodayAttendance($employee->id);

        if (!$todayAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'No active check-in found',
            ], 404);
        }

        $data = CheckOutData::fromRequest(
            $request->validated(),
            $employee->id,
            $todayAttendance->id
        );

        try {
            $this->attendanceService->checkOut($data);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check out successful',
        ]);
    }

    public function history(): JsonResponse
    {
        $user = request()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found',
            ], 404);
        }

        $filters = request()->only(['start_date', 'end_date']);
        $history = $this->attendanceService->getHistory($employee->id, $filters);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}
