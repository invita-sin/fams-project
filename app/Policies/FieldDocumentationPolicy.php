<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\FieldDocumentation;
use App\Models\User;

class FieldDocumentationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function view(User $user, FieldDocumentation $documentation): bool
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
                ->where('id', $documentation->employee_id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'supervisor']);
    }

    public function delete(User $user, FieldDocumentation $documentation): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin']);
    }

    public function uploadPhoto(User $user, FieldDocumentation $documentation): bool
    {
        return $this->view($user, $documentation);
    }
}
