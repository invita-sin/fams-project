<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateCorrectionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCorrectionRequest;
use App\Models\AttendanceCorrection;
use App\Models\Employee;
use App\Services\AttendanceCorrectionService;
use Illuminate\Http\JsonResponse;

class AttendanceCorrectionController extends Controller
{
    public function __construct(
        private readonly AttendanceCorrectionService $correctionService,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', AttendanceCorrection::class);

        $filters = request()->only(['employee_id', 'status']);
        $corrections = $this->correctionService->paginate(10, $filters);

        return response()->json([
            'success' => true,
            'data' => $corrections->items(),
            'meta' => [
                'current_page' => $corrections->currentPage(),
                'last_page' => $corrections->lastPage(),
                'per_page' => $corrections->perPage(),
                'total' => $corrections->total(),
            ],
        ]);
    }

    public function store(StoreCorrectionRequest $request): JsonResponse
    {
        $this->authorize('create', AttendanceCorrection::class);

        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found',
            ], 404);
        }

        $data = CreateCorrectionData::fromRequest($request->validated(), $employee->id);
        $correction = $this->correctionService->createCorrection($data, $employee->id);

        return response()->json([
            'success' => true,
            'message' => 'Correction submitted',
            'data' => $correction,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $correction = $this->correctionService->getCorrection($id);

        if (!$correction) {
            return response()->json([
                'success' => false,
                'message' => 'Correction not found',
            ], 404);
        }

        $this->authorize('view', $correction);

        return response()->json([
            'success' => true,
            'data' => $correction,
        ]);
    }

    public function approve(string $id): JsonResponse
    {
        $correction = $this->correctionService->getCorrection($id);

        if (!$correction) {
            return response()->json([
                'success' => false,
                'message' => 'Correction not found',
            ], 404);
        }

        $this->authorize('approve', $correction);

        try {
            $correction = $this->correctionService->approve($id, request()->user()->id);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Correction approved',
            'data' => $correction,
        ]);
    }

    public function reject(string $id, \App\Http\Requests\RejectCorrectionRequest $request): JsonResponse
    {
        $correction = $this->correctionService->getCorrection($id);

        if (!$correction) {
            return response()->json([
                'success' => false,
                'message' => 'Correction not found',
            ], 404);
        }

        $this->authorize('reject', $correction);

        try {
            $correction = $this->correctionService->reject(
                $id,
                $request->user()->id,
                $request->input('reason')
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Correction rejected',
            'data' => $correction,
        ]);
    }
}
