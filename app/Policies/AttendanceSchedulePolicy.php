<?php

namespace App\Policies;

use App\Models\User;

class AttendanceSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin']);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin']);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
