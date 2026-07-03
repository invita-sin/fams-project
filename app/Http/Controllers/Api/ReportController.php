<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FieldDocumentation;
use App\Models\Employee;
use App\Services\ReportGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportGeneratorService $reportService,
    ) {}

    public function attendance(): JsonResponse
    {
        $this->authorize('viewAny', Attendance::class);

        $filters = request()->only(['start_date', 'end_date', 'employee_id', 'branch_id', 'division_id']);
        $report = $this->reportService->attendanceReport($filters);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function documentations(): JsonResponse
    {
        $this->authorize('viewAny', FieldDocumentation::class);

        $filters = request()->only(['start_date', 'end_date', 'employee_id', 'category']);
        $report = $this->reportService->documentationReport($filters);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function employeeActivity(): JsonResponse
    {
        $this->authorize('viewAny', Employee::class);

        $filters = request()->only(['start_date', 'end_date', 'branch_id', 'division_id', 'status']);
        $report = $this->reportService->employeeActivityReport($filters);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function exportAttendance(Request $request): JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('viewAny', Attendance::class);

        $filters = $request->only(['start_date', 'end_date', 'employee_id', 'branch_id', 'division_id']);
        $format = $request->query('format', 'xlsx');

        $filePath = match ($format) {
            'xlsx' => $this->reportService->exportAttendanceXlsx($filters),
            'csv' => $this->reportService->exportAttendanceCsv($filters),
            'pdf' => $this->reportService->exportAttendancePdf($filters),
            default => throw new \InvalidArgumentException("Format tidak didukung: {$format}"),
        };

        $mime = match ($format) {
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv; charset=UTF-8',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };

        return response()->download($filePath, null, ['Content-Type' => $mime])->deleteFileAfterSend(true);
    }

    public function exportDocumentations(Request $request): JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('viewAny', FieldDocumentation::class);

        $filters = $request->only(['start_date', 'end_date', 'employee_id', 'category']);
        $format = $request->query('format', 'xlsx');

        $filePath = match ($format) {
            'xlsx' => $this->reportService->exportDocumentationsXlsx($filters),
            'csv' => $this->reportService->exportDocumentationsCsv($filters),
            'pdf' => $this->reportService->exportDocumentationsPdf($filters),
            default => throw new \InvalidArgumentException("Format tidak didukung: {$format}"),
        };

        $mime = match ($format) {
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv; charset=UTF-8',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };

        return response()->download($filePath, null, ['Content-Type' => $mime])->deleteFileAfterSend(true);
    }
}
