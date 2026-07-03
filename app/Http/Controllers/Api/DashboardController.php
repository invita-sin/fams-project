<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function admin(): JsonResponse
    {
        if (!request()->user()->hasAnyRole(['super_admin', 'hr_admin', 'supervisor'])) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully.',
            'data' => $this->dashboardService->adminData(),
        ]);
    }

    public function employee(): JsonResponse
    {
        $user = request()->user();

        if (!$user->hasRole('employee')) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully.',
            'data' => $this->dashboardService->employeeData($user->id),
        ]);
    }

    public function supervisor(): JsonResponse
    {
        $user = request()->user();

        if (!$user->hasRole('supervisor')) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully.',
            'data' => $this->dashboardService->supervisorData($user->id),
        ]);
    }
}
