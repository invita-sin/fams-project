<?php

namespace App\Services;

use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;
use Illuminate\Http\UploadedFile;

class EmployeeImportService
{
    public function __construct(
        private readonly EmployeeService $employeeService,
    ) {}

    public function import(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = match ($extension) {
            'xlsx', 'xls' => $this->readXlsx($file),
            'csv' => $this->readCsv($file),
            default => throw new \InvalidArgumentException('Format file tidak didukung. Gunakan xlsx atau csv.'),
        };

        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if (empty($row['full_name'])) {
                $errors[] = "Baris {$rowNumber}: Nama lengkap wajib diisi";
                continue;
            }

            try {
                $data = new \App\DTOs\ImportEmployeeData(
                    employeeCode: $row['employee_code'] ?? null,
                    fullName: $row['full_name'],
                    email: $row['email'] ?? null,
                    phone: $row['phone'] ?? null,
                    divisionId: $row['division_id'] ?? null,
                    positionId: $row['position_id'] ?? null,
                    status: $row['status'] ?? 'active',
                );

                $createData = new \App\DTOs\CreateEmployeeData(
                    employeeCode: $data->employeeCode ?? $this->generateEmployeeCode(),
                    fullName: $data->fullName,
                    phone: $data->phone,
                    gender: null,
                    branchId: null,
                    divisionId: $data->divisionId,
                    departmentId: null,
                    positionId: $data->positionId,
                    hireDate: today()->toDateString(),
                    status: $data->status,
                );

                $this->employeeService->createEmployee($createData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNumber}: {$e->getMessage()}";
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
            'total' => count($rows),
        ];
    }

    private function generateEmployeeCode(): string
    {
        return 'IMP-' . now()->format('YmdHis') . '-' . strtolower(\Illuminate\Support\Str::random(4));
    }

    private function readXlsx(UploadedFile $file): array
    {
        $reader = new XLSXReader();
        $reader->open($file->getPathname());

        return $this->readRows($reader);
    }

    private function readCsv(UploadedFile $file): array
    {
        $reader = new CSVReader();
        $reader->open($file->getPathname());

        return $this->readRows($reader);
    }

    private function readRows($reader): array
    {
        $rows = [];
        $headers = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                $cells = [];
                foreach ($row->getCells() as $cell) {
                    $cells[] = $cell->getValue();
                }

                if ($index === 1) {
                    $headers = $cells;
                    continue;
                }

                $rowData = [];
                foreach ($headers as $i => $header) {
                    $rowData[trim(strtolower(str_replace(' ', '_', $header)))] = $cells[$i] ?? null;
                }

                if (!empty(array_filter($rowData))) {
                    $rows[] = $rowData;
                }
            }
        }

        $reader->close();

        return $rows;
    }
}
