<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceSchedule;
use App\Models\Employee;
use App\Models\FieldDocumentation;
use App\Models\User;
use App\Models\WorkLocation;
use App\Policies\AttendanceCorrectionPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\AttendanceSchedulePolicy;
use App\Policies\EmployeePolicy;
use App\Policies\FieldDocumentationPolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkLocationPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(AttendanceCorrection::class, AttendanceCorrectionPolicy::class);
        Gate::policy(FieldDocumentation::class, FieldDocumentationPolicy::class);
        Gate::policy(AttendanceSchedule::class, AttendanceSchedulePolicy::class);
        Gate::policy(WorkLocation::class, WorkLocationPolicy::class);

        Response::macro('success', function (mixed $data = null, string $message = '', int $code = 200): JsonResponse {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $code);
        });

        Response::macro('error', function (string $message, int $code = 400, mixed $data = null): JsonResponse {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], $code);
        });
    }
}
