<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatsService;

class StatsApiController extends Controller
{
    public function __construct(
        private readonly StatsService $statsService,
    ) {}

    public function __invoke()
    {
        return response()->json($this->statsService->getApiStats());
    }
}
