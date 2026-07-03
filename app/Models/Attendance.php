<?php

namespace App\Models;

use App\Support\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'employee_id',
        'schedule_id',
        'attendance_date',
        'checkin_time',
        'checkout_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'checkin_time' => 'datetime',
            'checkout_time' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AttendanceSchedule::class, 'schedule_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AttendancePhoto::class);
    }
}
