<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\Subsidy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubsidyService
{
    public function getFilteredPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = Subsidy::query();

        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        return $query->orderByDesc('year')
            ->orderByDesc('amount')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAvailableYears()
    {
        return Subsidy::distinct()
            ->whereNotNull('year')
            ->orderByDesc('year')
            ->pluck('year');
    }

    public function getWithRelations(Subsidy $subsidy): array
    {
        $linkedEntities = Entity::whereHas('links', function ($query) use ($subsidy) {
            $query->where('linked_type', 'subsidy')
                ->where('linked_id', $subsidy->id);
        })->get();

        return [
            'subsidy' => $subsidy,
            'linkedEntities' => $linkedEntities,
        ];
    }
}
