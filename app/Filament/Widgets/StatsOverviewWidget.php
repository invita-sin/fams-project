<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $todayCheckins = Attendance::whereDate('attendance_date', today())->count();
        $pendingCorrections = AttendanceCorrection::where('status', 'pending')->count();

        return [
            Stat::make('Total Karyawan', $totalEmployees)
                ->description('Jumlah seluruh karyawan')
                ->color('info'),

            Stat::make('Hadir Hari Ini', $todayCheckins)
                ->description('Karyawan yang sudah check-in')
                ->color('success'),

            Stat::make('Menunggu Persetujuan', $pendingCorrections)
                ->description('Pengajuan koreksi absensi')
                ->color('warning'),
        ];
    }
}
