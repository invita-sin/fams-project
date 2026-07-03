<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceCorrectionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DocumentationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmployeeScheduleController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\WorkLocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,1');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::post('/employees', [EmployeeController::class, 'store']);
        Route::post('/employees/import', [EmployeeController::class, 'import']);
        Route::get('/employees/{id}', [EmployeeController::class, 'show']);
        Route::put('/employees/{id}', [EmployeeController::class, 'update']);
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

        Route::get('/employees/{id}/schedules', [EmployeeScheduleController::class, 'schedules']);
        Route::get('/employees/{id}/schedules/active', [EmployeeScheduleController::class, 'activeSchedule']);
        Route::post('/employees/{id}/schedules', [EmployeeScheduleController::class, 'assign']);
        Route::delete('/employees/{id}/schedules/{scheduleId}', [EmployeeScheduleController::class, 'remove']);

        Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/history', [AttendanceController::class, 'history']);

        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

        Route::get('/attendance-corrections', [AttendanceCorrectionController::class, 'index']);
        Route::post('/attendance-corrections', [AttendanceCorrectionController::class, 'store']);
        Route::get('/attendance-corrections/{id}', [AttendanceCorrectionController::class, 'show']);
        Route::post('/attendance-corrections/{id}/approve', [AttendanceCorrectionController::class, 'approve']);
        Route::post('/attendance-corrections/{id}/reject', [AttendanceCorrectionController::class, 'reject']);

        Route::get('/reports/attendance', [ReportController::class, 'attendance']);
        Route::get('/reports/attendance/export', [ReportController::class, 'exportAttendance']);
        Route::get('/reports/documentations', [ReportController::class, 'documentations']);
        Route::get('/reports/documentations/export', [ReportController::class, 'exportDocumentations']);
        Route::get('/reports/employee-activity', [ReportController::class, 'employeeActivity']);

        Route::get('/schedules', [ScheduleController::class, 'index']);
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
        Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);

        Route::get('/work-locations', [WorkLocationController::class, 'index']);
        Route::post('/work-locations', [WorkLocationController::class, 'store']);
        Route::get('/work-locations/{id}', [WorkLocationController::class, 'show']);
        Route::put('/work-locations/{id}', [WorkLocationController::class, 'update']);
        Route::delete('/work-locations/{id}', [WorkLocationController::class, 'destroy']);

        Route::get('/documentations', [DocumentationController::class, 'index']);
        Route::get('/dashboard/admin', [DashboardController::class, 'admin']);
        Route::get('/dashboard/employee', [DashboardController::class, 'employee']);
        Route::get('/dashboard/supervisor', [DashboardController::class, 'supervisor']);

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
            Route::get('unread-count', [NotificationController::class, 'unreadCount']);
        });
        Route::post('/documentations', [DocumentationController::class, 'store']);
        Route::get('/documentations/{id}', [DocumentationController::class, 'show']);
        Route::delete('/documentations/{id}', [DocumentationController::class, 'destroy']);
        Route::post('/documentations/{id}/photos', [DocumentationController::class, 'uploadPhoto']);
    });
});
