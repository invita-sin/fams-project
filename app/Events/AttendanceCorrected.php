<?php

namespace App\Events;

use App\Models\AttendanceCorrection;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class AttendanceCorrected
{
    use Dispatchable;

    public function __construct(
        public readonly AttendanceCorrection $correction,
        public readonly User $user,
        public readonly string $action,
    ) {}
}
