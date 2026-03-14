<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Services\EntityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntityApiController extends Controller
{
    public function __construct(
        private readonly EntityService $entityService,
    ) {}

    public function index(Request $request)
    {
        return $this->entityService->getFilteredPaginated(
            $request->all(),
            $request->input('per_page', 25),
        );
    }

    public function show(Entity $entity)
    {
        return $entity;
    }

    public function relations(Entity $entity)
    {
        return response()->json([
            'entity' => $entity,
            'relations' => $this->entityService->getRelationsGrouped($entity),
        ]);
    }

    public function timeline(Entity $entity): JsonResponse
    {
        return response()->json([
            'entity_id' => $entity->id,
            'entity_name' => $entity->name,
            'timeline' => $this->entityService->getTimeline($entity),
        ]);
    }
}
