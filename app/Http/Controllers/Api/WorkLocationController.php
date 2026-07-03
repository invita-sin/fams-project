<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateWorkLocationData;
use App\DTOs\UpdateWorkLocationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkLocationRequest;
use App\Http\Requests\UpdateWorkLocationRequest;
use App\Models\WorkLocation;
use App\Services\WorkLocationService;
use Illuminate\Http\JsonResponse;

class WorkLocationController extends Controller
{
    public function __construct(
        private readonly WorkLocationService $workLocationService,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', WorkLocation::class);

        return response()->json([
            'success' => true,
            'data' => $this->workLocationService->getAllWorkLocations(),
        ]);
    }

    public function store(StoreWorkLocationRequest $request): JsonResponse
    {
        $this->authorize('create', WorkLocation::class);

        $data = CreateWorkLocationData::fromRequest($request->validated());
        $location = $this->workLocationService->createWorkLocation($data);

        return response()->json([
            'success' => true,
            'message' => 'Work location created',
            'data' => $location->load(['branch', 'geofences']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $this->authorize('viewAny', WorkLocation::class);

        $location = $this->workLocationService->getWorkLocation($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Work location not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $location,
        ]);
    }

    public function update(UpdateWorkLocationRequest $request, string $id): JsonResponse
    {
        $this->authorize('update', WorkLocation::class);

        try {
            $data = UpdateWorkLocationData::fromRequest($request->validated());
            $location = $this->workLocationService->updateWorkLocation($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Work location updated',
                'data' => $location,
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
        $this->authorize('delete', WorkLocation::class);

        try {
            $this->workLocationService->deleteWorkLocation($id);

            return response()->json([
                'success' => true,
                'message' => 'Work location deleted',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
