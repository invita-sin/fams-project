<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Console\Command;

class MarkAbsentEmployees extends Command
{
    protected $signature = 'attendance:summary';
    protected $description = 'Mark active employees without attendance today as absent';

    public function handle(): int
    {
        $today = today()->toDateString();
        $activeEmployees = Employee::where('status', 'active')->get();
        $todaysAttendances = Attendance::where('attendance_date', $today)->pluck('employee_id')->toArray();

        $absentCount = 0;

        foreach ($activeEmployees as $employee) {
            if (!in_array($employee->id, $todaysAttendances)) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $today,
                    'status' => 'absent',
                ]);
                $absentCount++;
            }
        }

        $this->info("Marked {$absentCount} employees as absent for {$today}.");

        return Command::SUCCESS;
    }
}
