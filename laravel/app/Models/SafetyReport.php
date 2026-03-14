<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyReport extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'category',
        'description',
        'reporter_name',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }
}
