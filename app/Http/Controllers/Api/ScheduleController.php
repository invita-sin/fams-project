<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateScheduleData;
use App\DTOs\UpdateScheduleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\AttendanceSchedule;
use App\Services\ScheduleService;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    public function __construct(
        private readonly ScheduleService $scheduleService,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', AttendanceSchedule::class);

        return response()->json([
            'success' => true,
            'data' => $this->scheduleService->getAllSchedules(),
        ]);
    }

    public function store(StoreScheduleRequest $request): JsonResponse
    {
        $this->authorize('create', AttendanceSchedule::class);

        $data = CreateScheduleData::fromRequest($request->validated());
        $schedule = $this->scheduleService->createSchedule($data);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created',
            'data' => $schedule,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $this->authorize('viewAny', AttendanceSchedule::class);

        $schedule = $this->scheduleService->getSchedule($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    public function update(UpdateScheduleRequest $request, string $id): JsonResponse
    {
        $this->authorize('update', AttendanceSchedule::class);

        try {
            $data = UpdateScheduleData::fromRequest($request->validated());
            $schedule = $this->scheduleService->updateSchedule($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated',
                'data' => $schedule,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $this->authorize('delete', AttendanceSchedule::class);

        try {
            $this->scheduleService->deleteSchedule($id);

            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
