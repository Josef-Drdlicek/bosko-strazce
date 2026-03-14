<?php

namespace App\Http\Controllers;

use App\Services\StatsService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StatsService $statsService,
    ) {}

    public function __invoke()
    {
        return view('dashboard', $this->statsService->getDashboardData());
    }
}
