<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\FieldDocumentation;

class SupervisorDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.supervisor-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard Supervisor';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasRole('supervisor');
    }

    public function getTeamStats(): array
    {
        $supervisor = auth()->user()?->employee;

        if (!$supervisor) {
            return [
                'total_team' => 0,
                'today_present' => 0,
                'pending_approvals' => 0,
                'today_documentations' => 0,
            ];
        }

        $teamIds = Employee::where('supervisor_id', $supervisor->id)->pluck('id');

        $totalTeam = $teamIds->count();
        $todayPresent = Attendance::whereIn('employee_id', $teamIds)
            ->whereDate('attendance_date', today())
            ->distinct('employee_id')
            ->count('employee_id');

        $pendingApprovals = AttendanceCorrection::whereIn('employee_id', $teamIds)
            ->where('status', 'pending')
            ->count();

        $todayDocumentations = FieldDocumentation::whereIn('employee_id', $teamIds)
            ->whereDate('created_at', today())
            ->count();

        return [
            'total_team' => $totalTeam,
            'today_present' => $todayPresent,
            'pending_approvals' => $pendingApprovals,
            'today_documentations' => $todayDocumentations,
        ];
    }
}
