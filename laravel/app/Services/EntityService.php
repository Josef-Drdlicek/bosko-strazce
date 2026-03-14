<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\EntityLink;
use App\Models\Subsidy;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EntityService
{
    public function getFilteredPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = Entity::withCount('links');

        if (! empty($filters['q'])) {
            $query->search($filters['q']);
        }

        if (! empty($filters['type'])) {
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
        $documents = $this->resolveLinked(Document::class, $grouped->get('document'));
        $subsidies = $this->resolveLinked(Subsidy::class, $grouped->get('subsidy'));
        $relatedEntities = $this->resolveLinked(Entity::class, $grouped->get('entity'));

        return [
            'entity' => $entity,
            'links' => $links,
            'documents' => $documents,
            'contracts' => $contracts,
            'subsidies' => $subsidies,
            'relatedEntities' => $relatedEntities,
            'reverseEntityLinks' => $this->buildReverseEntityLinks($entity),
            'contractRoles' => $this->buildRoleMap($grouped->get('contract')),
            'documentRoles' => $this->buildRoleMap($grouped->get('document')),
            'subsidyRoles' => $this->buildRoleMap($grouped->get('subsidy')),
            'entityRoles' => $this->buildRoleMap($grouped->get('entity')),
            'aggregated' => $this->buildAggregatedStats($contracts, $subsidies),
            'timeline' => $this->buildTimeline($contracts, $subsidies),
        ];
    }

    public function getRelationsGrouped(Entity $entity): Collection
    {
        return $entity->links->groupBy('linked_type')->map(function (Collection $items, string $type) {
            $models = $this->resolveLinkedModels($type, $items->pluck('linked_id'));

            return $items->map(fn ($link) => [
                'role' => $link->role,
                'type' => $type,
                'linked' => $models->get($link->linked_id),
            ]);
        });
    }

    public function getTimeline(Entity $entity): Collection
    {
        $links = $entity->links;
        $grouped = $links->groupBy('linked_type');

        $contracts = $this->resolveLinked(Contract::class, $grouped->get('contract'));
        $subsidies = $this->resolveLinked(Subsidy::class, $grouped->get('subsidy'));

        return $this->buildTimeline($contracts, $subsidies);
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
        $modelClass = match ($type) {
            'document' => Document::class,
            'contract' => Contract::class,
            'subsidy' => Subsidy::class,
            'entity' => Entity::class,
            default => null,
        };

        if ($modelClass === null) {
            return collect();
        }

        return $modelClass::whereIn('id', $ids)->get()->keyBy('id');
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

        return $links->mapWithKeys(fn ($link) => [$link->linked_id => $link->role]);
    }

    private function buildAggregatedStats(Collection $contracts, Collection $subsidies): array
    {
        $contractAmounts = $contracts->pluck('amount')->filter();

        return [
            'contract_count' => $contracts->count(),
            'contract_total' => $contractAmounts->sum(),
            'contract_avg' => $contractAmounts->avg() ? round($contractAmounts->avg(), 2) : 0,
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
                    'date' => Carbon::create($subsidy->year, 1, 1),
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
