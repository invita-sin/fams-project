<?php

namespace App\Models;

use App\Support\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldDocumentation extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'category',
        'latitude',
        'longitude',
        'activity_date',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DocumentationPhoto::class, 'documentation_id');
    }
}
