<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'document_id',
        'url',
        'filename',
        'local_path',
        'size_bytes',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
