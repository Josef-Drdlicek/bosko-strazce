<?php

namespace App\Http\Controllers;

use App\Models\Subsidy;
use App\Services\SubsidyService;
use Illuminate\Http\Request;

class SubsidyController extends Controller
{
    public function __construct(
        private readonly SubsidyService $subsidyService,
    ) {}

    public function index(Request $request)
    {
        return view('subsidies.index', [
            'subsidies' => $this->subsidyService->getFilteredPaginated($request->all()),
            'years' => $this->subsidyService->getAvailableYears(),
            'currentYear' => $request->input('year'),
            'searchQuery' => $request->input('q'),
        ]);
    }

    public function show(Subsidy $subsidy)
    {
        return view('subsidies.show', $this->subsidyService->getWithRelations($subsidy));
    }
}
