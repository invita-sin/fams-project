<?php

namespace App\Models;

use App\Support\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttendanceSchedule extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'checkin_start',
        'checkin_end',
        'checkout_time',
    ];

    protected function casts(): array
    {
        return [
            'checkin_start' => 'string',
            'checkin_end' => 'string',
            'checkout_time' => 'string',
        ];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'attendance_schedule_employee', 'schedule_id', 'employee_id')
            ->withPivot(['effective_date', 'end_date', 'id'])
            ->withTimestamps();
    }
}
