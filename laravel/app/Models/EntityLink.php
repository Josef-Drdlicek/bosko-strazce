<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityLink extends Model
{
    protected $guarded = [];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function linked(): Model|null
    {
        return match ($this->linked_type) {
            'document' => Document::find($this->linked_id),
            'contract' => Contract::find($this->linked_id),
            'subsidy' => Subsidy::find($this->linked_id),
            default => null,
        };
    }
}
