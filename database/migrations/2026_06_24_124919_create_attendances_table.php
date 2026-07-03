<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('schedule_id')->nullable()->constrained('attendance_schedules')->nullOnDelete();
            $table->date('attendance_date');
            $table->timestamp('checkin_time')->nullable();
            $table->timestamp('checkout_time')->nullable();
            $table->string('status', 50)->default('present');
            $table->timestamps();

            $table->index('attendance_date');
            $table->index('status');
            $table->unique(['employee_id', 'attendance_date']);
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendance_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 50);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy', 10, 2)->nullable();
            $table->decimal('distance_meter', 10, 2)->nullable();
            $table->json('device_info')->nullable();
            $table->string('ip_address', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('event_type');
        });

        Schema::create('attendance_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendance_id')->constrained()->cascadeOnDelete();
            $table->string('photo_type', 50);
            $table->foreignUuid('media_file_id')->constrained('media_files')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_photos');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('attendances');
    }
};
