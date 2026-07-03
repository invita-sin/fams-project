<?php

namespace App\Listeners;

use App\Events\AttendanceCheckedIn;
use App\Events\AttendanceCorrected;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class CreateAuditLogListener
{
    public function handleAttendanceCheckedIn(AttendanceCheckedIn $event): void
    {
        AuditLog::create([
            'auditable_id' => $event->attendance->id,
            'auditable_type' => $event->attendance->getMorphClass(),
            'user_id' => $event->user->id,
            'event' => 'attendance.checked_in',
            'new_values' => $event->attendance->toArray(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function handleAttendanceCorrected(AttendanceCorrected $event): void
    {
        AuditLog::create([
            'auditable_id' => $event->correction->id,
            'auditable_type' => $event->correction->getMorphClass(),
            'user_id' => $event->user->id,
            'event' => 'attendance.correction_' . $event->action,
            'new_values' => $event->correction->toArray(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
