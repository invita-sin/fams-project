<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateDocumentationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentationRequest;
use App\Http\Requests\UploadPhotoRequest;
use App\Models\Employee;
use App\Models\FieldDocumentation;
use App\Services\DocumentationService;
use Illuminate\Http\JsonResponse;

class DocumentationController extends Controller
{
    public function __construct(
        private readonly DocumentationService $documentationService,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', FieldDocumentation::class);

        $filters = request()->only(['employee_id', 'start_date', 'end_date', 'category']);
        $documentations = $this->documentationService->paginate(null, 10, $filters);

        return response()->json([
            'success' => true,
            'data' => $documentations->items(),
            'meta' => [
                'current_page' => $documentations->currentPage(),
                'last_page' => $documentations->lastPage(),
                'per_page' => $documentations->perPage(),
                'total' => $documentations->total(),
            ],
        ]);
    }

    public function store(StoreDocumentationRequest $request): JsonResponse
    {
        $this->authorize('create', FieldDocumentation::class);

        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found',
            ], 404);
        }

        $data = CreateDocumentationData::fromRequest($request->validated(), $employee->id);
        $documentation = $this->documentationService->createDocumentation($data);

        return response()->json([
            'success' => true,
            'message' => 'Documentation created',
            'data' => $documentation,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $documentation = $this->documentationService->getDocumentation($id);

        if (!$documentation) {
            return response()->json([
                'success' => false,
                'message' => 'Documentation not found',
            ], 404);
        }

        $this->authorize('view', $documentation);

        return response()->json([
            'success' => true,
            'data' => $documentation,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $documentation = $this->documentationService->getDocumentation($id);

        if (!$documentation) {
            return response()->json([
                'success' => false,
                'message' => 'Documentation not found',
            ], 404);
        }

        $this->authorize('delete', $documentation);

        try {
            $this->documentationService->deleteDocumentation($id);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Documentation deleted',
        ]);
    }

    public function uploadPhoto(string $id, UploadPhotoRequest $request): JsonResponse
    {
        $documentation = $this->documentationService->getDocumentation($id);

        if (!$documentation) {
            return response()->json([
                'success' => false,
                'message' => 'Documentation not found',
            ], 404);
        }

        $this->authorize('uploadPhoto', $documentation);

        try {
            $photo = $this->documentationService->uploadPhoto(
                $id,
                $request->input('media_file_id'),
                $request->input('sort_order', 0)
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded',
            'data' => $photo,
        ]);
    }
}
