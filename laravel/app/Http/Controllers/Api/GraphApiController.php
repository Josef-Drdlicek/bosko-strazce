<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Services\GraphService;
use Illuminate\Http\JsonResponse;

class GraphApiController extends Controller
{
    public function __construct(
        private readonly GraphService $graphService,
    ) {}

    public function __invoke(Entity $entity): JsonResponse
    {
        return response()->json($this->graphService->buildGraph($entity));
    }
}
