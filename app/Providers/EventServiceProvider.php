<?php

namespace App\Providers;

use App\Events\AttendanceCheckedIn;
use App\Events\AttendanceCorrected;
use App\Listeners\CreateAuditLogListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AttendanceCheckedIn::class => [
            CreateAuditLogListener::class . '@handleAttendanceCheckedIn',
        ],
        AttendanceCorrected::class => [
            CreateAuditLogListener::class . '@handleAttendanceCorrected',
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
