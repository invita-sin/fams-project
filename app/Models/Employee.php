<?php

namespace App\Models;

use App\Support\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'employee_code',
        'branch_id',
        'division_id',
        'department_id',
        'position_id',
        'supervisor_id',
        'user_id',
        'full_name',
        'phone',
        'gender',
        'hire_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function supervisedEmployees(): HasMany
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function fieldDocumentations(): HasMany
    {
        return $this->hasMany(FieldDocumentation::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(AttendanceSchedule::class, 'attendance_schedule_employee', 'employee_id', 'schedule_id')
            ->withPivot(['effective_date', 'end_date', 'id'])
            ->withTimestamps();
    }

    public function activeSchedule(): ?AttendanceSchedule
    {
        return $this->schedules()
            ->wherePivot('effective_date', '<=', today()->toDateString())
            ->where(function ($q) {
                $q->whereNull('attendance_schedule_employee.end_date')
                  ->orWhere('attendance_schedule_employee.end_date', '>=', today()->toDateString());
            })
            ->first();
    }
}
