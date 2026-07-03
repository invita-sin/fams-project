<?php

namespace App\Services;

use App\Exceptions\Domain\EmployeeNotFoundException;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Employee;
use App\Models\FieldDocumentation;
use App\Repositories\EmployeeRepositoryInterface;

class DashboardService
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
    ) {}

    public function adminData(): array
    {
        return [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'today_attendance' => Attendance::whereDate('attendance_date', today())->count(),
            'pending_corrections' => AttendanceCorrection::where('status', 'pending')->count(),
            'total_documentations' => FieldDocumentation::count(),
            'today_documentations' => FieldDocumentation::whereDate('created_at', today())->count(),
        ];
    }

    public function employeeData(string $userId): array
    {
        $employee = Employee::where('user_id', $userId)->first();

        if (!$employee) {
            throw new EmployeeNotFoundException('Employee profile not found');
        }

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->first();

        $thisMonthAttendance = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->count();

        $thisMonthLate = Attendance::where('employee_id', $employee->id)
            ->where('status', 'late')
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->count();

        $pendingCorrections = AttendanceCorrection::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        return [
            'today_checked_in' => $todayAttendance && $todayAttendance->checkin_time,
            'today_checked_out' => $todayAttendance && $todayAttendance->checkout_time,
            'this_month_attendance' => $thisMonthAttendance,
            'this_month_late' => $thisMonthLate,
            'pending_corrections' => $pendingCorrections,
        ];
    }

    public function supervisorData(string $userId): array
    {
        $supervisor = Employee::where('user_id', $userId)->first();

        if (!$supervisor) {
            throw new EmployeeNotFoundException('Supervisor profile not found');
        }

        $teamIds = Employee::where('supervisor_id', $supervisor->id)->pluck('id');

        return [
            'total_team' => $teamIds->count(),
            'today_present' => Attendance::whereIn('employee_id', $teamIds)
                ->whereDate('attendance_date', today())
                ->distinct('employee_id')
                ->count('employee_id'),
            'pending_approvals' => AttendanceCorrection::whereIn('employee_id', $teamIds)
                ->where('status', 'pending')
                ->count(),
            'today_documentations' => FieldDocumentation::whereIn('employee_id', $teamIds)
                ->whereDate('created_at', today())
                ->count(),
        ];
    }
}
