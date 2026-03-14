<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Entity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContractService
{
    public function getFilteredPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = Contract::query();

        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        $sortField = $filters['sort'] ?? 'date_signed';
        $sortDirection = $filters['dir'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getWithRelations(Contract $contract): array
    {
        $linkedEntities = Entity::whereHas('links', function ($query) use ($contract) {
            $query->where('linked_type', 'contract')
                ->where('linked_id', $contract->id);
        })->get();

        return [
            'contract' => $contract,
            'linkedEntities' => $linkedEntities,
        ];
    }
}
