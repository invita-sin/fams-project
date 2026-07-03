<?php

namespace App\Providers;

use App\Repositories\AttendanceRepository;
use App\Repositories\AttendanceRepositoryInterface;
use App\Repositories\DocumentationRepository;
use App\Repositories\AttendanceCorrectionRepository;
use App\Repositories\AttendanceCorrectionRepositoryInterface;
use App\Repositories\DocumentationRepositoryInterface;
use App\Repositories\EmployeeRepository;
use App\Repositories\EmployeeRepositoryInterface;
use App\Repositories\ScheduleRepository;
use App\Repositories\ScheduleRepositoryInterface;
use App\Repositories\WorkLocationRepository;
use App\Repositories\WorkLocationRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(DocumentationRepositoryInterface::class, DocumentationRepository::class);
        $this->app->bind(AttendanceCorrectionRepositoryInterface::class, AttendanceCorrectionRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(WorkLocationRepositoryInterface::class, WorkLocationRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
