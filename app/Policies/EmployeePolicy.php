<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin']);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin']);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin']);
    }
}
