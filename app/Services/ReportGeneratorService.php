<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\FieldDocumentation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;

class ReportGeneratorService
{
    public function attendanceReport(array $filters): Collection
    {
        $query = Attendance::with('employee')
            ->select('employee_id', DB::raw('DATE(attendance_date) as date'), 'status', 'checkin_time', 'checkout_time');

        if (!empty($filters['start_date'])) {
            $query->where('attendance_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('attendance_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', fn ($q) => $q->where('branch_id', $filters['branch_id']));
        }

        if (!empty($filters['division_id'])) {
            $query->whereHas('employee', fn ($q) => $q->where('division_id', $filters['division_id']));
        }

        $records = $query->orderBy('attendance_date', 'desc')->get();

        return $records->groupBy('employee_id')->map(function ($items, $employeeId) {
            $employee = $items->first()->employee;

            return [
                'employee_id' => $employeeId,
                'employee_name' => $employee?->full_name ?? 'Unknown',
                'employee_code' => $employee?->employee_code ?? '',
                'total_present' => $items->where('status', 'present')->count(),
                'total_late' => $items->where('status', 'late')->count(),
                'total_absent' => $items->where('status', 'absent')->count(),
                'total_days' => $items->count(),
                'records' => $items->map(fn ($a) => [
                    'date' => $a->date,
                    'status' => $a->status,
                    'checkin' => $a->checkin_time?->format('H:i:s'),
                    'checkout' => $a->checkout_time?->format('H:i:s'),
                ]),
            ];
        })->values();
    }

    public function documentationReport(array $filters): Collection
    {
        $query = FieldDocumentation::with('employee', 'photos');

        if (!empty($filters['start_date'])) {
            $query->where('activity_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('activity_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        $records = $query->orderBy('activity_date', 'desc')->get();

        return $records->groupBy('employee_id')->map(function ($items, $employeeId) {
            $employee = $items->first()->employee;

            return [
                'employee_id' => $employeeId,
                'employee_name' => $employee?->full_name ?? 'Unknown',
                'employee_code' => $employee?->employee_code ?? '',
                'total_documentations' => $items->count(),
                'total_photos' => $items->sum(fn ($d) => $d->photos->count()),
                'records' => $items->map(fn ($d) => [
                    'id' => $d->id,
                    'title' => $d->title,
                    'category' => $d->category,
                    'activity_date' => $d->activity_date?->toDateString(),
                    'photos_count' => $d->photos->count(),
                ]),
            ];
        })->values();
    }

    public function employeeActivityReport(array $filters): Collection
    {
        $query = Employee::query();

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['division_id'])) {
            $query->where('division_id', $filters['division_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $attendanceFilter = fn ($q) => $q;
        $docFilter = fn ($q) => $q;
        if (!empty($filters['start_date'])) {
            $attendanceFilter = fn ($q) => $q->where('attendance_date', '>=', $filters['start_date']);
            $docFilter = fn ($q) => $q->where('activity_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $attendanceFilter = fn ($q) => $q->where('attendance_date', '<=', $filters['end_date']);
            $docFilter = fn ($q) => $q->where('activity_date', '<=', $filters['end_date']);
        }

        $employees = $query->withCount([
            'attendances as total_attendances' => $attendanceFilter,
            'fieldDocumentations as total_documentations' => $docFilter,
        ])->get();

        return $employees->map(fn (Employee $employee) => [
            'employee_id' => $employee->id,
            'employee_name' => $employee->full_name,
            'employee_code' => $employee->employee_code,
            'status' => $employee->status,
            'total_attendances' => (int) $employee->total_attendances,
            'total_documentations' => (int) $employee->total_documentations,
        ])->sortByDesc('total_attendances')->values();
    }

    public function exportAttendanceXlsx(array $filters): string
    {
        $data = $this->attendanceReport($filters);

        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/attendance-report-' . now()->format('YmdHis') . '.xlsx';

        $writer = new XLSXWriter();
        $writer->openToFile($filePath);

        $writer->addRow(Row::fromValues([
            'Employee ID', 'Employee Name', 'Employee Code',
            'Total Present', 'Total Late', 'Total Absent', 'Total Days',
        ]));

        foreach ($data as $row) {
            $writer->addRow(Row::fromValues([
                $row['employee_id'],
                $row['employee_name'],
                $row['employee_code'],
                $row['total_present'],
                $row['total_late'],
                $row['total_absent'],
                $row['total_days'],
            ]));
        }

        $writer->close();

        return $filePath;
    }

    public function exportAttendanceCsv(array $filters): string
    {
        $data = $this->attendanceReport($filters);

        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/attendance-report-' . now()->format('YmdHis') . '.csv';

        $writer = new CSVWriter();
        $writer->openToFile($filePath);

        $writer->addRow(Row::fromValues([
            'Employee ID', 'Employee Name', 'Employee Code',
            'Total Present', 'Total Late', 'Total Absent', 'Total Days',
        ]));

        foreach ($data as $row) {
            $writer->addRow(Row::fromValues([
                $row['employee_id'],
                $row['employee_name'],
                $row['employee_code'],
                $row['total_present'],
                $row['total_late'],
                $row['total_absent'],
                $row['total_days'],
            ]));
        }

        $writer->close();

        return $filePath;
    }

    public function exportDocumentationsXlsx(array $filters): string
    {
        $data = $this->documentationReport($filters);

        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/documentation-report-' . now()->format('YmdHis') . '.xlsx';

        $writer = new XLSXWriter();
        $writer->openToFile($filePath);

        $writer->addRow(Row::fromValues([
            'Employee ID', 'Employee Name', 'Employee Code',
            'Total Documentations', 'Total Photos',
        ]));

        foreach ($data as $row) {
            $writer->addRow(Row::fromValues([
                $row['employee_id'],
                $row['employee_name'],
                $row['employee_code'],
                $row['total_documentations'],
                $row['total_photos'],
            ]));
        }

        $writer->close();

        return $filePath;
    }

    public function exportDocumentationsCsv(array $filters): string
    {
        $data = $this->documentationReport($filters);

        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/documentation-report-' . now()->format('YmdHis') . '.csv';

        $writer = new CSVWriter();
        $writer->openToFile($filePath);

        $writer->addRow(Row::fromValues([
            'Employee ID', 'Employee Name', 'Employee Code',
            'Total Documentations', 'Total Photos',
        ]));

        foreach ($data as $row) {
            $writer->addRow(Row::fromValues([
                $row['employee_id'],
                $row['employee_name'],
                $row['employee_code'],
                $row['total_documentations'],
                $row['total_photos'],
            ]));
        }

        $writer->close();

        return $filePath;
    }

    public function exportAttendancePdf(array $filters): string
    {
        $data = $this->attendanceReport($filters);

        $html = $this->renderAttendanceHtml($data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/attendance-report-' . now()->format('YmdHis') . '.pdf';
        file_put_contents($filePath, $dompdf->output());

        return $filePath;
    }

    public function exportDocumentationsPdf(array $filters): string
    {
        $data = $this->documentationReport($filters);

        $html = $this->renderDocumentationHtml($data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/documentation-report-' . now()->format('YmdHis') . '.pdf';
        file_put_contents($filePath, $dompdf->output());

        return $filePath;
    }

    private function renderAttendanceHtml(Collection $data): string
    {
        $rows = '';
        foreach ($data as $item) {
            $rows .= '<tr>';
            $rows .= '<td>' . e($item['employee_code'] ?? '') . '</td>';
            $rows .= '<td>' . e($item['employee_name'] ?? '') . '</td>';
            $rows .= '<td>' . ($item['total_present'] ?? 0) . '</td>';
            $rows .= '<td>' . ($item['total_late'] ?? 0) . '</td>';
            $rows .= '<td>' . ($item['total_absent'] ?? 0) . '</td>';
            $rows .= '<td>' . ($item['total_days'] ?? 0) . '</td>';
            $rows .= '</tr>';
        }

        return '
    <html>
    <head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
    </style>
    </head>
    <body>
        <h1>Laporan Absensi</h1>
        <p>Periode: ' . now()->format('d/m/Y') . '</p>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Hadir</th>
                    <th>Terlambat</th>
                    <th>Tidak Hadir</th>
                    <th>Total Hari</th>
                </tr>
            </thead>
            <tbody>' . $rows . '</tbody>
        </table>
    </body>
    </html>';
    }

    private function renderDocumentationHtml(Collection $data): string
    {
        $rows = '';
        foreach ($data as $item) {
            $rows .= '<tr>';
            $rows .= '<td>' . e($item['employee_code'] ?? '') . '</td>';
            $rows .= '<td>' . e($item['employee_name'] ?? '') . '</td>';
            $rows .= '<td>' . ($item['total_documentations'] ?? 0) . '</td>';
            $rows .= '<td>' . ($item['total_photos'] ?? 0) . '</td>';
            $rows .= '</tr>';
        }

        return '
    <html>
    <head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
    </style>
    </head>
    <body>
        <h1>Laporan Dokumentasi Lapangan</h1>
        <p>Periode: ' . now()->format('d/m/Y') . '</p>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Total Dokumentasi</th>
                    <th>Total Foto</th>
                </tr>
            </thead>
            <tbody>' . $rows . '</tbody>
        </table>
    </body>
    </html>';
    }
}
