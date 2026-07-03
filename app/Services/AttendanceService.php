<?php

namespace App\Services;

use App\DTOs\CheckInData;
use App\DTOs\CheckOutData;
use App\Events\AttendanceCheckedIn;
use App\Exceptions\Domain\AlreadyCheckedInException;
use App\Exceptions\Domain\CheckInTooEarlyException;
use App\Exceptions\Domain\EmployeeNotFoundException;
use App\Exceptions\Domain\ScheduleNotFoundException;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendancePhoto;
use App\Models\Employee;
use App\Models\MediaFile;
use App\Repositories\AttendanceRepositoryInterface;
use App\Repositories\EmployeeRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendanceRepository,
        private readonly EmployeeScheduleService $scheduleService,
        private readonly EmployeeRepositoryInterface $employeeRepository,
    ) {}

    public function checkIn(CheckInData $data): Attendance
    {
        $existingToday = $this->attendanceRepository->findToday($data->employeeId);

        if ($existingToday) {
            throw new AlreadyCheckedInException();
        }

        $employee = $this->employeeRepository->findById($data->employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException();
        }

        $activeSchedule = $employee->activeSchedule();

        if (!$activeSchedule) {
            throw new ScheduleNotFoundException();
        }

        $now = now();
        $checkInTime = $now->format('H:i:s');

        $isLate = $activeSchedule->checkin_end && $checkInTime > $activeSchedule->checkin_end;
        $isEarly = $activeSchedule->checkin_start && $checkInTime < $activeSchedule->checkin_start;

        if ($isEarly) {
            throw new CheckInTooEarlyException('Check-in too early. Schedule starts at ' . $activeSchedule->checkin_start);
        }

        if (!$data->photo) {
            throw new \RuntimeException('Foto selfie wajib diambil');
        }

        $attendance = DB::transaction(function () use ($data, $isLate, $employee) {
            $attendance = $this->attendanceRepository->create([
                'employee_id' => $data->employeeId,
                'attendance_date' => today(),
                'checkin_time' => now(),
                'status' => $isLate ? 'late' : 'present',
            ]);

            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'event_type' => 'checkin',
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'accuracy' => $data->accuracy,
            ]);

            $this->saveAttendancePhoto($attendance, $data->photo, 'checkin');

            return $attendance;
        });

        if (request()->user()) {
            event(new AttendanceCheckedIn($attendance, request()->user()));
        }

        return $attendance;
    }

    public function checkOut(CheckOutData $data): Attendance
    {
        $attendance = $this->attendanceRepository->findToday($data->employeeId);

        if (!$attendance || $attendance->id !== $data->attendanceId) {
            throw new \RuntimeException('No active check-in found');
        }

        if ($attendance->checkout_time) {
            throw new \RuntimeException('Already checked out');
        }

        if (!$data->photo) {
            throw new \RuntimeException('Foto selfie wajib diambil');
        }

        return DB::transaction(function () use ($data, $attendance) {
            $attendance = $this->attendanceRepository->update($attendance, [
                'checkout_time' => now(),
            ]);

            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'event_type' => 'checkout',
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'accuracy' => $data->accuracy,
            ]);

            $this->saveAttendancePhoto($attendance, $data->photo, 'checkout');

            return $attendance;
        });
    }

    public function getHistory(string $employeeId, array $filters = []): Collection
    {
        return $this->attendanceRepository->getHistory($employeeId, $filters);
    }

    public function getTodayAttendance(string $employeeId): ?Attendance
    {
        return $this->attendanceRepository->findToday($employeeId);
    }

    private function saveAttendancePhoto(Attendance $attendance, mixed $photo, string $photoType): void
    {
        if (!$photo instanceof UploadedFile) {
            return;
        }

        $folderName = 'attendance-photos/' . $attendance->id;
        $fileName = $photoType . '_' . now()->format('YmdHis') . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

        $path = $photo->storeAs($folderName, $fileName, 'public');

        $mediaFile = MediaFile::create([
            'file_name' => $fileName,
            'original_name' => $photo->getClientOriginalName(),
            'mime_type' => $photo->getMimeType(),
            'file_size' => $photo->getSize(),
            'storage_disk' => 'public',
            'storage_path' => $path,
        ]);

        AttendancePhoto::create([
            'attendance_id' => $attendance->id,
            'photo_type' => $photoType,
            'media_file_id' => $mediaFile->id,
        ]);
    }
}
