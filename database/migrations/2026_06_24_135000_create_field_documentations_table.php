<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_documentations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->date('activity_date');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('documentation_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('documentation_id')->constrained('field_documentations')->cascadeOnDelete();
            $table->foreignUuid('media_file_id')->constrained('media_files')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation_photos');
        Schema::dropIfExists('field_documentations');
    }
};
