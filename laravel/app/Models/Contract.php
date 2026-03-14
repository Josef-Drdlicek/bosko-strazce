<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'external_id',
        'subject',
        'amount',
        'currency',
        'date_signed',
        'publisher_ico',
        'publisher_name',
        'counterparty_ico',
        'counterparty_name',
        'source_url',
        'fulltext',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date_signed' => 'date',
        ];
    }

    public function entityLinks(): HasMany
    {
        return $this->hasMany(EntityLink::class, 'linked_id')
            ->where('linked_type', 'contract');
    }

    public function linkedEntities()
    {
        return Entity::whereHas('links', function ($query) {
            $query->where('linked_type', 'contract')
                ->where('linked_id', $this->id);
        });
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('subject', 'like', "%{$term}%")
                ->orWhere('counterparty_name', 'like', "%{$term}%")
                ->orWhere('fulltext', 'like', "%{$term}%");
        });
    }
}
