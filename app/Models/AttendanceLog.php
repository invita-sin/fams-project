<?php

namespace App\Models;

use App\Support\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'attendance_id',
        'event_type',
        'latitude',
        'longitude',
        'accuracy',
        'distance_meter',
        'device_info',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy' => 'decimal:2',
            'distance_meter' => 'decimal:2',
            'device_info' => 'array',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
