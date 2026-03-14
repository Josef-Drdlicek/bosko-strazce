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

    public function getLinkedModelAttribute(): ?Model
    {
        return match ($this->linked_type) {
            'document' => Document::find($this->linked_id),
            'contract' => Contract::find($this->linked_id),
            'subsidy' => Subsidy::find($this->linked_id),
            default => null,
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabelFor($this->role);
    }

    public static function roleLabelFor(string $role): string
    {
        return match ($role) {
            'publisher' => 'Objednatel',
            'counterparty' => 'Dodavatel',
            'mentioned' => 'Zmíněn v dokumentu',
            'recipient' => 'Příjemce dotace',
            'statutory' => 'Statutární zástupce',
            'shareholder' => 'Společník',
            'council_member' => 'Zastupitel',
            'board_member' => 'Radní',
            'committee_member' => 'Člen komise',
            'owner' => 'Vlastník',
            'tenant' => 'Nájemce',
            'implementor' => 'Realizátor',
            'funded_by' => 'Financováno z',
            default => $role,
        };
    }
}
