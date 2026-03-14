<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata_json' => 'array',
        ];
    }

    public function links(): HasMany
    {
        return $this->hasMany(EntityLink::class);
    }

    public function contractLinks(): HasMany
    {
        return $this->hasMany(EntityLink::class)->where('linked_type', 'contract');
    }

    public function documentLinks(): HasMany
    {
        return $this->hasMany(EntityLink::class)->where('linked_type', 'document');
    }

    public function subsidyLinks(): HasMany
    {
        return $this->hasMany(EntityLink::class)->where('linked_type', 'subsidy');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('ico', 'like', "%{$term}%");
        });
    }

    public function hasAresData(): bool
    {
        return is_array($this->metadata_json) && !empty($this->metadata_json);
    }
}
