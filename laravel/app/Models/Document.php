<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'valid_until' => 'date',
            'collected_at' => 'datetime',
        ];
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'duplicate_of');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(Document::class, 'duplicate_of');
    }

    public function entityLinks(): HasMany
    {
        return $this->hasMany(EntityLink::class, 'linked_id')
            ->where('linked_type', 'document');
    }

    public function linkedEntities()
    {
        return Entity::whereHas('links', function ($query) {
            $query->where('linked_type', 'document')
                ->where('linked_id', $this->id);
        });
    }

    public function scopeOriginals($query)
    {
        return $query->whereNull('duplicate_of');
    }

    public function scopeSection($query, string $section)
    {
        return $query->where('section', $section);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('fulltext', 'like', "%{$term}%");
        });
    }
}
