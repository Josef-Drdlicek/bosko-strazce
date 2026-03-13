<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $guarded = [];

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
