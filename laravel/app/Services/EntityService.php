<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\EntityLink;
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

        $contracts = $this->resolveLinked(Contract::class, $grouped->get('contract'));
        $subsidies = $this->resolveLinked(Subsidy::class, $grouped->get('subsidy'));

        $contractRoles = $this->buildRoleMap($grouped->get('contract'));
        $documentRoles = $this->buildRoleMap($grouped->get('document'));
        $subsidyRoles = $this->buildRoleMap($grouped->get('subsidy'));

        $relatedEntities = $this->resolveLinked(Entity::class, $grouped->get('entity'));
        $entityRoles = $this->buildRoleMap($grouped->get('entity'));

        $reverseEntityLinks = $this->buildReverseEntityLinks($entity);

        return [
            'entity' => $entity,
            'links' => $links,
            'documents' => $this->resolveLinked(Document::class, $grouped->get('document')),
            'contracts' => $contracts,
            'subsidies' => $subsidies,
            'relatedEntities' => $relatedEntities,
            'reverseEntityLinks' => $reverseEntityLinks,
            'contractRoles' => $contractRoles,
            'documentRoles' => $documentRoles,
            'subsidyRoles' => $subsidyRoles,
            'entityRoles' => $entityRoles,
            'aggregated' => $this->buildAggregatedStats($contracts, $subsidies),
            'timeline' => $this->buildTimeline($contracts, $subsidies),
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
            'entity' => Entity::whereIn('id', $ids)->get()->keyBy('id'),
            default => collect(),
        };
    }

    private function buildReverseEntityLinks(Entity $entity): Collection
    {
        return EntityLink::where('linked_type', 'entity')
            ->where('linked_id', $entity->id)
            ->with('entity')
            ->get()
            ->map(fn (EntityLink $link) => (object) [
                'entity' => $link->entity,
                'role' => $link->role,
                'role_label' => $link->role_label,
            ]);
    }

    private function buildRoleMap(?Collection $links): Collection
    {
        if ($links === null || $links->isEmpty()) {
            return collect();
        }

        return $links->mapWithKeys(fn($link) => [$link->linked_id => $link->role]);
    }

    private function buildAggregatedStats(Collection $contracts, Collection $subsidies): array
    {
        $contractAmounts = $contracts->pluck('amount')->filter();

        return [
            'contract_count' => $contracts->count(),
            'contract_total' => $contractAmounts->sum(),
            'contract_avg' => $contractAmounts->count() > 0 ? round($contractAmounts->avg(), 2) : 0,
            'contract_min_date' => $contracts->pluck('date_signed')->filter()->min(),
            'contract_max_date' => $contracts->pluck('date_signed')->filter()->max(),
            'subsidy_count' => $subsidies->count(),
            'subsidy_total' => $subsidies->pluck('amount')->filter()->sum(),
        ];
    }

    private function buildTimeline(Collection $contracts, Collection $subsidies): Collection
    {
        $items = collect();

        foreach ($contracts as $contract) {
            if ($contract->date_signed) {
                $items->push((object) [
                    'date' => $contract->date_signed,
                    'type' => 'contract',
                    'label' => $contract->subject ?: 'Smlouva',
                    'amount' => $contract->amount,
                    'id' => $contract->id,
                ]);
            }
        }

        foreach ($subsidies as $subsidy) {
            if ($subsidy->year) {
                $items->push((object) [
                    'date' => \Carbon\Carbon::create($subsidy->year, 1, 1),
                    'type' => 'subsidy',
                    'label' => $subsidy->title ?: 'Dotace',
                    'amount' => $subsidy->amount,
                    'id' => $subsidy->id,
                ]);
            }
        }

        return $items->sortByDesc('date')->values();
    }
}
