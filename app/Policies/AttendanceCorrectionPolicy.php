<?php

namespace App\Policies;

use App\Models\AttendanceCorrection;
use App\Models\Employee;
use App\Models\User;

class AttendanceCorrectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function view(User $user, AttendanceCorrection $correction): bool
    {
        if ($user->hasAnyRole(['super_admin', 'hr_admin'])) {
            return true;
        }

        if ($user->hasRole('supervisor')) {
            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                return false;
            }
            return $employee->supervisedEmployees()
                ->where('id', $correction->employee_id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function approve(User $user, AttendanceCorrection $correction): bool
    {
        if ($user->hasAnyRole(['super_admin', 'hr_admin'])) {
            return true;
        }

        if ($user->hasRole('supervisor')) {
            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                return false;
            }
            return $employee->supervisedEmployees()
                ->where('id', $correction->employee_id)
                ->exists();
        }

        return false;
    }

    public function reject(User $user, AttendanceCorrection $correction): bool
    {
        return $this->approve($user, $correction);
    }
}
