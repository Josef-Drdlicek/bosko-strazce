<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EntityService
{
    public function getFilteredPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = Entity::withCount('links');

        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        if (!empty($filters['type'])) {
            $query->where('entity_type', $filters['type']);
        }

        return $query->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAvailableTypes(): Collection
    {
        return Entity::distinct()
            ->pluck('entity_type')
            ->sort()
            ->values();
    }

    public function getWithRelations(Entity $entity): array
    {
        $links = $entity->links;
        $grouped = $links->groupBy('linked_type');

        return [
            'entity' => $entity,
            'links' => $links,
            'documents' => $this->resolveLinked(Document::class, $grouped->get('document')),
            'contracts' => $this->resolveLinked(Contract::class, $grouped->get('contract')),
            'subsidies' => $this->resolveLinked(Subsidy::class, $grouped->get('subsidy')),
        ];
    }

    public function getRelationsGrouped(Entity $entity): Collection
    {
        return $entity->links->groupBy('linked_type')->map(function (Collection $items, string $type) {
            $ids = $items->pluck('linked_id');
            $models = $this->resolveLinkedModels($type, $ids);

            return $items->map(fn($link) => [
                'role' => $link->role,
                'type' => $type,
                'linked' => $models->get($link->linked_id),
            ]);
        });
    }

    private function resolveLinked(string $modelClass, ?Collection $links): Collection
    {
        if ($links === null || $links->isEmpty()) {
            return collect();
        }

        return $modelClass::whereIn('id', $links->pluck('linked_id'))->get();
    }

    private function resolveLinkedModels(string $type, Collection $ids): Collection
    {
        return match ($type) {
            'document' => Document::whereIn('id', $ids)->get()->keyBy('id'),
            'contract' => Contract::whereIn('id', $ids)->get()->keyBy('id'),
            'subsidy' => Subsidy::whereIn('id', $ids)->get()->keyBy('id'),
            default => collect(),
        };
    }
}
