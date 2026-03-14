<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Services\GraphService;

class GraphController extends Controller
{
    public function __construct(
        private readonly GraphService $graphService,
    ) {}

    public function show(Entity $entity)
    {
        return view('graph.show', [
            'entity' => $entity,
            'graphData' => $this->graphService->buildGraph($entity),
        ]);
    }
}
