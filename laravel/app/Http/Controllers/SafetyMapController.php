<?php

namespace App\Http\Controllers;

use App\Services\SafetyReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SafetyMapController extends Controller
{
    public function __construct(
        private readonly SafetyReportService $safetyReportService,
    ) {}

    public function index(): View
    {
        return view('safety-map.index', [
            'categoryCounts' => $this->safetyReportService->getCategoryCounts(),
            'totalReports' => $this->safetyReportService->getAllReports()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:49.0,50.0'],
            'longitude' => ['required', 'numeric', 'between:16.0,17.5'],
            'category' => ['required', 'string', 'in:lighting,sidewalk,traffic,vandalism,other'],
            'description' => ['required', 'string', 'max:1000'],
            'reporter_name' => ['nullable', 'string', 'max:100'],
        ]);

        $this->safetyReportService->createReport($validated);

        return redirect()->route('safety-map.index')->with('success', 'Děkujeme za váš podnět!');
    }

    public function geojson(): JsonResponse
    {
        return response()->json(
            $this->safetyReportService->getReportsAsGeoJson()
        );
    }
}
