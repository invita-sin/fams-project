<?php

namespace App\Services;

use App\DTOs\CreateCorrectionData;
use App\Events\AttendanceCorrected;
use App\Exceptions\Domain\ResourceNotFoundException;
use App\Models\AttendanceCorrection;
use App\Models\User;
use App\Notifications\AttendanceCorrectionApproved;
use App\Notifications\AttendanceCorrectionRejected;
use App\Notifications\AttendanceCorrectionSubmitted;
use App\Repositories\AttendanceCorrectionRepositoryInterface;
use App\Repositories\EmployeeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Notification;

class AttendanceCorrectionService
{
    public function __construct(
        private readonly AttendanceCorrectionRepositoryInterface $correctionRepository,
        private readonly EmployeeRepositoryInterface $employeeRepository,
    ) {}

    public function createCorrection(CreateCorrectionData $data, string $employeeId): AttendanceCorrection
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new ResourceNotFoundException('Employee not found');
        }

        $payload = $data->toArray();
        $payload['employee_id'] = $employeeId;

        $correction = $this->correctionRepository->create($payload);

        $correction->loadMissing(['employee.user', 'employee']);

        $reviewers = User::role(['supervisor', 'hr_admin'])->get();

        Notification::send($reviewers, new AttendanceCorrectionSubmitted($correction));

        if (request()->user()) {
            event(new AttendanceCorrected($correction, request()->user(), 'submitted'));
        }

        return $correction;
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->correctionRepository->paginate($perPage, $filters);
    }

    public function getCorrection(string $id): ?AttendanceCorrection
    {
        return $this->correctionRepository->findById($id);
    }

    public function approve(string $id, string $reviewedBy): AttendanceCorrection
    {
        $correction = $this->correctionRepository->findById($id);

        if (!$correction) {
            throw new ResourceNotFoundException('Correction not found');
        }

        if ($correction->status !== 'pending') {
            throw new \RuntimeException('Correction is already ' . $correction->status);
        }

        $correction = $this->correctionRepository->update($correction, [
            'status' => 'approved',
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
        ]);

        $correction->loadMissing(['employee.user', 'reviewer']);

        if ($correction->employee?->user) {
            $correction->employee->user->notify(new AttendanceCorrectionApproved($correction));
        }

        if (request()->user()) {
            event(new AttendanceCorrected($correction, request()->user(), 'approved'));
        }

        return $correction;
    }

    public function reject(string $id, string $reviewedBy, string $reason): AttendanceCorrection
    {
        $correction = $this->correctionRepository->findById($id);

        if (!$correction) {
            throw new ResourceNotFoundException('Correction not found');
        }

        if ($correction->status !== 'pending') {
            throw new \RuntimeException('Correction is already ' . $correction->status);
        }

        $correction = $this->correctionRepository->update($correction, [
            'status' => 'rejected',
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $correction->loadMissing(['employee.user', 'reviewer']);

        if ($correction->employee?->user) {
            $correction->employee->user->notify(new AttendanceCorrectionRejected($correction));
        }

        if (request()->user()) {
            event(new AttendanceCorrected($correction, request()->user(), 'rejected'));
        }

        return $correction;
    }
}
