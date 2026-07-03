<?php

namespace Tests\Feature\Unit;

use App\Events\AttendanceCheckedIn;
use App\Events\AttendanceCorrected;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_checked_in_event_is_dispatched(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create();

        Event::fake();

        event(new AttendanceCheckedIn($attendance, $user));

        Event::assertDispatched(AttendanceCheckedIn::class);
    }

    public function test_attendance_corrected_event_is_dispatched(): void
    {
        $user = User::factory()->create();
        $correction = AttendanceCorrection::factory()->create();

        Event::fake();

        event(new AttendanceCorrected($correction, $user, 'approved'));

        Event::assertDispatched(AttendanceCorrected::class);
    }

    public function test_audit_log_is_created_on_checkin_event(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create();

        event(new AttendanceCheckedIn($attendance, $user));

        $this->assertDatabaseHas('audit_logs', [
            'auditable_id' => $attendance->id,
            'auditable_type' => $attendance->getMorphClass(),
            'user_id' => $user->id,
            'event' => 'attendance.checked_in',
        ]);
    }

    public function test_audit_log_is_created_on_correction_event(): void
    {
        $user = User::factory()->create();
        $correction = AttendanceCorrection::factory()->create();

        event(new AttendanceCorrected($correction, $user, 'approved'));

        $this->assertDatabaseHas('audit_logs', [
            'auditable_id' => $correction->id,
            'auditable_type' => $correction->getMorphClass(),
            'user_id' => $user->id,
            'event' => 'attendance.correction_approved',
        ]);
    }

}
