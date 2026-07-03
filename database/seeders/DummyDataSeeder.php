<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceSchedule;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\FieldDocumentation;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::create([
            'code' => 'BR-001',
            'name' => 'Kantor Pusat',
            'active' => true,
        ]);

        $divOperational = Division::create([
            'branch_id' => $branch->id,
            'name' => 'Operasional',
        ]);

        $divIT = Division::create([
            'branch_id' => $branch->id,
            'name' => 'Teknologi Informasi',
        ]);

        $deptLapangan = Department::create([
            'division_id' => $divOperational->id,
            'name' => 'Lapangan',
        ]);

        $deptSupport = Department::create([
            'division_id' => $divOperational->id,
            'name' => 'Dukungan Operasional',
        ]);

        $deptEngineering = Department::create([
            'division_id' => $divIT->id,
            'name' => 'Rekayasa Perangkat Lunak',
        ]);

        $posStaff = Position::create([
            'department_id' => $deptLapangan->id,
            'name' => 'Staff Lapangan',
        ]);

        $posSupervisor = Position::create([
            'department_id' => $deptLapangan->id,
            'name' => 'Supervisor Lapangan',
        ]);

        $posEngineer = Position::create([
            'department_id' => $deptEngineering->id,
            'name' => 'Engineer',
        ]);

        $workLocations = [];
        $locations = [
            ['code' => 'WL-001', 'name' => 'Gudang Utara', 'latitude' => -6.2088, 'longitude' => 106.8456],
            ['code' => 'WL-002', 'name' => 'Kantor Cabang Selatan', 'latitude' => -6.3088, 'longitude' => 106.7456],
            ['code' => 'WL-003', 'name' => 'Depo Timur', 'latitude' => -6.1088, 'longitude' => 106.9456],
        ];

        foreach ($locations as $loc) {
            $workLocations[] = WorkLocation::create([
                'branch_id' => $branch->id,
                'name' => $loc['name'],
                'address' => $loc['name'],
                'latitude' => $loc['latitude'],
                'longitude' => $loc['longitude'],
                'radius_meter' => 100,
                'active' => true,
            ]);
        }

        $schedule = AttendanceSchedule::create([
            'name' => 'Shift Pagi',
            'checkin_start' => '07:00:00',
            'checkin_end' => '08:00:00',
            'checkout_time' => '16:00:00',
        ]);

        $hrUser = User::factory()->create([
            'name' => 'Budi HR',
            'email' => 'hr_admin@fams.test',
            'password' => bcrypt('password'),
        ]);
        $hrUser->assignRole('hr_admin');

        $supervisor1User = User::factory()->create([
            'name' => 'Ahmad Supervisor',
            'email' => 'supervisor1@fams.test',
            'password' => bcrypt('password'),
        ]);
        $supervisor1User->assignRole('supervisor');

        $supervisor1 = Employee::create([
            'employee_code' => 'EMP-0001',
            'full_name' => 'Ahmad Supervisor',
            'phone' => '081234567890',
            'branch_id' => $branch->id,
            'division_id' => $divOperational->id,
            'department_id' => $deptLapangan->id,
            'position_id' => $posSupervisor->id,
            'user_id' => $supervisor1User->id,
            'status' => 'active',
        ]);

        $supervisor2User = User::factory()->create([
            'name' => 'Siti Supervisor',
            'email' => 'supervisor2@fams.test',
            'password' => bcrypt('password'),
        ]);
        $supervisor2User->assignRole('supervisor');

        $supervisor2 = Employee::create([
            'employee_code' => 'EMP-0002',
            'full_name' => 'Siti Supervisor',
            'phone' => '081234567891',
            'branch_id' => $branch->id,
            'division_id' => $divIT->id,
            'department_id' => $deptEngineering->id,
            'position_id' => $posEngineer->id,
            'user_id' => $supervisor2User->id,
            'status' => 'active',
        ]);

        $employees = [];
        $employeeData = [
            ['code' => 'EMP-0003', 'name' => 'Rudi Karyawan', 'email' => 'employee1@fams.test', 'sup' => 0],
            ['code' => 'EMP-0004', 'name' => 'Dewi Karyawan', 'email' => 'employee2@fams.test', 'sup' => 0],
            ['code' => 'EMP-0005', 'name' => 'Hendra Karyawan', 'email' => 'employee3@fams.test', 'sup' => 0],
            ['code' => 'EMP-0006', 'name' => 'Rina Karyawan', 'email' => 'employee4@fams.test', 'sup' => 0],
            ['code' => 'EMP-0007', 'name' => 'Adi Karyawan', 'email' => 'employee5@fams.test', 'sup' => 1],
        ];

        foreach ($employeeData as $i => $data) {
            $user = User::factory()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('employee');

            $supervisorId = $data['sup'] === 0 ? $supervisor1->id : $supervisor2->id;

            $employee = Employee::create([
                'employee_code' => $data['code'],
                'full_name' => $data['name'],
                'phone' => '08123456789' . ($i + 2),
                'branch_id' => $branch->id,
                'division_id' => $i < 3 ? $divOperational->id : $divIT->id,
                'department_id' => $i < 3 ? $deptLapangan->id : $deptEngineering->id,
                'position_id' => $posStaff->id,
                'supervisor_id' => $supervisorId,
                'user_id' => $user->id,
                'status' => 'active',
            ]);

            $employee->schedules()->attach($schedule->id, [
                'id' => (string) Str::uuid(),
                'effective_date' => now()->subMonth(),
                'end_date' => null,
            ]);

            $employees[] = $employee;
        }

        foreach ($employees as $employee) {
            for ($day = 4; $day >= 0; $day--) {
                $date = now()->subDays($day);
                if ($date->isWeekend()) {
                    continue;
                }

                Attendance::create([
                    'employee_id' => $employee->id,
                    'schedule_id' => $schedule->id,
                    'attendance_date' => $date,
                    'checkin_time' => $date->format('Y-m-d') . ' 07:' . fake()->randomElement(['15', '25', '35', '45']) . ':00',
                    'checkout_time' => $date->format('Y-m-d') . ' 16:' . fake()->randomElement(['05', '10', '15']) . ':00',
                    'status' => 'present',
                ]);
            }
        }

        $datesWithCorrection = [now()->subDays(3), now()->subDays(1)];
        foreach ($datesWithCorrection as $i => $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $attendance = Attendance::where('employee_id', $employees[$i]->id)
                ->whereDate('attendance_date', $date)
                ->first();

            if ($attendance) {
                AttendanceCorrection::create([
                    'attendance_id' => $attendance->id,
                    'employee_id' => $employees[$i]->id,
                    'reason' => 'Terlambat karena macet parah di jalan tol',
                    'status' => $i === 0 ? 'approved' : 'pending',
                ]);
            }
        }

        $docCategories = ['visit', 'report', 'maintenance', 'delivery'];
        foreach ($employees as $employee) {
            for ($i = 0; $i < 3; $i++) {
                $date = now()->subDays(rand(0, 4));
                if ($date->isWeekend()) {
                    continue;
                }

                FieldDocumentation::create([
                    'employee_id' => $employee->id,
                    'title' => fake()->randomElement([
                        'Kunjungan Rutin ke Lokasi',
                        'Pengecekan Peralatan',
                        'Laporan Harian',
                        'Pengiriman Dokumen',
                        'Perbaikan Fasilitas',
                    ]),
                    'description' => fake()->paragraph(),
                    'category' => $docCategories[array_rand($docCategories)],
                    'latitude' => -6.2 + fake()->randomFloat(6, -0.05, 0.05),
                    'longitude' => 106.8 + fake()->randomFloat(6, -0.05, 0.05),
                    'activity_date' => $date,
                ]);
            }
        }
    }
}
