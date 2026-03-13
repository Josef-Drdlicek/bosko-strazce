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

    public function contracts()
    {
        return Contract::whereIn('id', $this->links()->where('linked_type', 'contract')->pluck('linked_id'));
    }

    public function documents()
    {
        return Document::whereIn('id', $this->links()->where('linked_type', 'document')->pluck('linked_id'));
    }

    public function subsidies()
    {
        return Subsidy::whereIn('id', $this->links()->where('linked_type', 'subsidy')->pluck('linked_id'));
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('ico', 'like', "%{$term}%");
    }
}
