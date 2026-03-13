<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'stats' => [
                'documents' => Document::originals()->count(),
                'contracts' => Contract::count(),
                'entities' => Entity::count(),
                'subsidies' => Subsidy::count(),
            ],
            'recentDocuments' => Document::originals()
                ->latest('published_date')
                ->limit(10)
                ->get(),
            'recentContracts' => Contract::latest('date_signed')
                ->limit(10)
                ->get(),
            'topCounterparties' => Contract::selectRaw('counterparty_name, counterparty_ico, COUNT(*) as contract_count, SUM(amount) as total_amount')
                ->whereNotNull('counterparty_name')
                ->groupBy('counterparty_name', 'counterparty_ico')
                ->orderByDesc('total_amount')
                ->limit(10)
                ->get(),
        ]);
    }
}
