<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;

class StatsApiController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'documents' => Document::originals()->count(),
            'contracts' => Contract::count(),
            'entities' => Entity::count(),
            'subsidies' => Subsidy::count(),
            'sections' => Document::originals()
                ->selectRaw('section, COUNT(*) as count')
                ->groupBy('section')
                ->pluck('count', 'section'),
            'contracts_total_amount' => Contract::sum('amount'),
            'subsidies_total_amount' => Subsidy::sum('amount'),
        ]);
    }
}
