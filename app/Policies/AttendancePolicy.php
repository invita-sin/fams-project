<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasAnyRole(['super_admin', 'hr_admin'])) {
            return true;
        }

        return $user->id === ($attendance->employee?->user_id);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['employee', 'supervisor']);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin']);
    }
}
