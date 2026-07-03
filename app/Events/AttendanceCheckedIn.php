<?php

namespace App\Events;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class AttendanceCheckedIn
{
    use Dispatchable;

    public function __construct(
        public readonly Attendance $attendance,
        public readonly User $user,
    ) {}
}
