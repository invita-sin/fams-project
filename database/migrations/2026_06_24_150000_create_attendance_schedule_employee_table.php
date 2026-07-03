<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_schedule_employee', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('schedule_id')->constrained('attendance_schedules')->cascadeOnDelete();
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'schedule_id', 'effective_date'], 'ase_emp_sch_eff_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_schedule_employee');
    }
};
