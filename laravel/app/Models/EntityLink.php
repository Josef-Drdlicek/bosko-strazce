<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityLink extends Model
{
    protected $fillable = [
        'entity_id',
        'linked_type',
        'linked_id',
        'role',
    ];

    private const ROLE_LABELS = [
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
        'chairman' => 'Předseda',
        'vice_chairman' => 'Místopředseda',
        'supervisory_member' => 'Člen kontrolního orgánu',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabelFor($this->role);
    }

    public static function roleLabelFor(string $role): string
    {
        return self::ROLE_LABELS[$role] ?? $role;
    }
}
