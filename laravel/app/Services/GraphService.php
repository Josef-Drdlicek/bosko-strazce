<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\EntityLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GraphService
{
    public function buildGraph(Entity $entity, int $depth = 1): array
    {
        $nodes = collect();
        $edges = collect();
        $visited = collect([$entity->id]);

        $this->addEntityNode($nodes, $entity, true);
        $this->expandEntity($nodes, $edges, $visited, $entity, $depth);

        return [
            'nodes' => $nodes->values()->toArray(),
            'edges' => $edges->values()->toArray(),
        ];
    }

    private function expandEntity(
        Collection $nodes,
        Collection $edges,
        Collection $visited,
        Entity $entity,
        int $remainingDepth,
    ): void {
        if ($remainingDepth <= 0) {
            return;
        }

        $links = $entity->links()->get();

        $linkedByType = $links->groupBy('linked_type');

        foreach ($linkedByType as $type => $typeLinks) {
            $linkedIds = $typeLinks->pluck('linked_id')->unique();

            $coEntities = $this->findCoEntities($type, $linkedIds, $entity->id);

            foreach ($coEntities as $coEntity) {
                if (!$visited->contains($coEntity->id)) {
                    $visited->push($coEntity->id);
                    $this->addEntityNode($nodes, $coEntity, false);
                }

                $sharedCount = $coEntity->shared_count ?? 1;
                $edgeKey = $this->edgeKey($entity->id, $coEntity->id, $type);

                if (!$edges->has($edgeKey)) {
                    $edges[$edgeKey] = [
                        'source' => $entity->id,
                        'target' => $coEntity->id,
                        'type' => $type,
                        'weight' => $sharedCount,
                        'label' => $this->edgeLabel($type, $sharedCount),
                    ];
                }
            }
        }
    }

    /**
     * Find entities that share linked records (contracts/documents/subsidies) with the source entity.
     */
    private function findCoEntities(string $linkedType, Collection $linkedIds, int $excludeEntityId): Collection
    {
        if ($linkedIds->isEmpty()) {
            return collect();
        }

        return Entity::select('entities.*')
            ->selectRaw('COUNT(DISTINCT entity_links.linked_id) as shared_count')
            ->join('entity_links', 'entities.id', '=', 'entity_links.entity_id')
            ->where('entity_links.linked_type', $linkedType)
            ->whereIn('entity_links.linked_id', $linkedIds)
            ->where('entities.id', '!=', $excludeEntityId)
            ->groupBy('entities.id')
            ->orderByDesc('shared_count')
            ->limit(30)
            ->get();
    }

    private function addEntityNode(Collection $nodes, Entity $entity, bool $isCentral): void
    {
        if ($nodes->has($entity->id)) {
            return;
        }

        $totalAmount = $this->entityTotalAmount($entity->id);

        $nodes[$entity->id] = [
            'id' => $entity->id,
            'name' => $entity->name,
            'ico' => $entity->ico,
            'type' => $entity->entity_type,
            'central' => $isCentral,
            'linksCount' => $entity->links()->count(),
            'totalAmount' => $totalAmount,
            'radius' => $this->calculateRadius($totalAmount),
        ];
    }

    private function entityTotalAmount(int $entityId): float
    {
        return (float) DB::table('entity_links')
            ->join('contracts', function ($join) {
                $join->on('entity_links.linked_id', '=', 'contracts.id')
                    ->where('entity_links.linked_type', '=', 'contract');
            })
            ->where('entity_links.entity_id', $entityId)
            ->sum('contracts.amount');
    }

    private function calculateRadius(float $totalAmount): int
    {
        if ($totalAmount <= 0) {
            return 8;
        }

        $log = log10(max($totalAmount, 1));

        return (int) min(max($log * 3, 8), 40);
    }

    private function edgeKey(int $sourceId, int $targetId, string $type): string
    {
        $ids = [$sourceId, $targetId];
        sort($ids);

        return implode('-', $ids) . '-' . $type;
    }

    private function edgeLabel(string $type, int $count): string
    {
        $label = match ($type) {
            'contract' => 'smluv',
            'document' => 'dokumentů',
            'subsidy' => 'dotací',
            default => $type,
        };

        return $count . ' ' . $label;
    }
}
