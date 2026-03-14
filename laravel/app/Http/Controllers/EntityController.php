<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Services\AresService;
use App\Services\EntityService;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly AresService $aresService,
    ) {}

    public function index(Request $request)
    {
        return view('entities.index', [
            'entities' => $this->entityService->getFilteredPaginated($request->all()),
            'types' => $this->entityService->getAvailableTypes(),
            'currentType' => $request->input('type'),
            'searchQuery' => $request->input('q'),
        ]);
    }

    public function show(Entity $entity)
    {
        if ($entity->ico && !$entity->hasAresData()) {
            $this->aresService->enrichEntity($entity);
            $entity->refresh();
        }

        $data = $this->entityService->getWithRelations($entity);

        return view('entities.show', $data);
    }
}
