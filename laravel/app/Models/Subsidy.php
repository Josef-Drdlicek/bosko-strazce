<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subsidy extends Model
{
    protected $fillable = [
        'external_id',
        'title',
        'provider',
        'recipient_ico',
        'recipient_name',
        'program',
        'amount',
        'year',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function entityLinks(): HasMany
    {
        return $this->hasMany(EntityLink::class, 'linked_id')
            ->where('linked_type', 'subsidy');
    }

    public function linkedEntities()
    {
        return Entity::whereHas('links', function ($query) {
            $query->where('linked_type', 'subsidy')
                ->where('linked_id', $this->id);
        });
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('recipient_name', 'like', "%{$term}%")
                ->orWhere('program', 'like', "%{$term}%");
        });
    }
}
