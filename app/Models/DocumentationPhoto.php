<?php

namespace App\Models;

use App\Support\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentationPhoto extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'documentation_id',
        'media_file_id',
        'sort_order',
    ];

    public function documentation(): BelongsTo
    {
        return $this->belongsTo(FieldDocumentation::class, 'documentation_id');
    }

    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class);
    }
}
