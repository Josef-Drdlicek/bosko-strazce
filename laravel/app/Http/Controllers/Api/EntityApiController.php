<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;
use Illuminate\Http\Request;

class EntityApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Entity::query();

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('type')) {
            $query->where('entity_type', $request->input('type'));
        }

        return $query->paginate($request->input('per_page', 25));
    }

    public function show(Entity $entity)
    {
        return $entity;
    }

    public function relations(Entity $entity)
    {
        $links = $entity->links()->get();

        $grouped = $links->groupBy('linked_type')->map(function ($items, $type) {
            $ids = $items->pluck('linked_id');
            $models = match ($type) {
                'document' => Document::whereIn('id', $ids)->get()->keyBy('id'),
                'contract' => Contract::whereIn('id', $ids)->get()->keyBy('id'),
                'subsidy' => Subsidy::whereIn('id', $ids)->get()->keyBy('id'),
                default => collect(),
            };

            return $items->map(fn ($link) => [
                'role' => $link->role,
                'type' => $type,
                'linked' => $models->get($link->linked_id),
            ]);
        });

        return response()->json([
            'entity' => $entity,
            'relations' => $grouped,
        ]);
    }
}
