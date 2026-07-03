<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use App\DTOs\CheckInData;
use App\DTOs\CheckOutData;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Employee;
use App\Services\AttendanceService;

class EmployeeDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.employee-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard Karyawan';
    protected static ?int $navigationSort = 1;

    public ?float $checkinLatitude = null;
    public ?float $checkinLongitude = null;
    public ?float $checkinAccuracy = null;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasRole('employee');
    }

    public function getStats(): array
    {
        $employee = auth()->user()?->employee;
        if (!$employee) {
            return [
                'today_checked_in' => false,
                'today_checked_out' => false,
                'this_month_attendance' => 0,
                'this_month_late' => 0,
                'pending_corrections' => 0,
            ];
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

    public function checkin(string $photoData): void
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        if (!$employee) {
            Notification::make()
                ->title('Profil karyawan tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $photo = $this->createUploadedFileFromBase64($photoData);

        $service = app(AttendanceService::class);
        $checkInData = new CheckInData(
            employeeId: $employee->id,
            latitude: (float) ($this->checkinLatitude ?? 0),
            longitude: (float) ($this->checkinLongitude ?? 0),
            accuracy: (float) ($this->checkinAccuracy ?? 0),
            notes: '',
            photo: $photo,
        );

        try {
            $service->checkIn($checkInData);
        } catch (\RuntimeException $e) {
            $this->dispatch('checkin-error', message: $e->getMessage());
            return;
        }

        $this->dispatch('checkin-success', message: 'Check In Berhasil');
    }

    public function checkout(string $photoData): void
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        if (!$employee) {
            $this->dispatch('checkin-error', message: 'Profil karyawan tidak ditemukan');
            return;
        }

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (!$todayAttendance) {
            $this->dispatch('checkin-error', message: 'Belum ada check-in hari ini');
            return;
        }

        $photo = $this->createUploadedFileFromBase64($photoData);

        $service = app(AttendanceService::class);
        $checkOutData = new CheckOutData(
            employeeId: $employee->id,
            attendanceId: $todayAttendance->id,
            latitude: (float) ($this->checkinLatitude ?? 0),
            longitude: (float) ($this->checkinLongitude ?? 0),
            accuracy: (float) ($this->checkinAccuracy ?? 0),
            notes: '',
            photo: $photo,
        );

        try {
            $service->checkOut($checkOutData);
        } catch (\RuntimeException $e) {
            $this->dispatch('checkin-error', message: $e->getMessage());
            return;
        }

        $this->dispatch('checkin-success', message: 'Check Out Berhasil');
    }

    private function createUploadedFileFromBase64(string $base64): \Illuminate\Http\UploadedFile
    {
        $decoded = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));
        $tmpPath = sys_get_temp_dir() . '/selfie_' . uniqid() . '.png';
        file_put_contents($tmpPath, $decoded);

        return new \Illuminate\Http\UploadedFile(
            $tmpPath,
            'selfie.png',
            'image/png',
            null,
            true
        );
    }
}
