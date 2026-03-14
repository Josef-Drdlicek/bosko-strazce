<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;
use Illuminate\Support\Collection;

class StatsService
{
    private const RECENT_ITEMS_LIMIT = 10;
    private const TOP_COUNTERPARTIES_LIMIT = 10;

    public function getDashboardData(): array
    {
        return [
            'stats' => $this->getCounts(),
            'recentDocuments' => $this->getRecentDocuments(),
            'recentContracts' => $this->getRecentContracts(),
            'topCounterparties' => $this->getTopCounterparties(),
        ];
    }

    public function getCounts(): array
    {
        return [
            'documents' => Document::originals()->count(),
            'contracts' => Contract::count(),
            'entities' => Entity::count(),
            'subsidies' => Subsidy::count(),
            'contracts_total' => (float) Contract::sum('amount'),
            'subsidies_total' => (float) Subsidy::sum('amount'),
        ];
    }

    public function getApiStats(): array
    {
        $counts = $this->getCounts();

        $counts['sections'] = Document::originals()
            ->selectRaw('section, COUNT(*) as count')
            ->groupBy('section')
            ->pluck('count', 'section');

        return $counts;
    }

    private function getRecentDocuments(): Collection
    {
        return Document::originals()
            ->latest('published_date')
            ->limit(self::RECENT_ITEMS_LIMIT)
            ->get();
    }

    private function getRecentContracts(): Collection
    {
        return Contract::latest('date_signed')
            ->limit(self::RECENT_ITEMS_LIMIT)
            ->get();
    }

    private function getTopCounterparties(): Collection
    {
        return Contract::selectRaw(
            'counterparty_name, counterparty_ico, COUNT(*) as contract_count, SUM(amount) as total_amount'
        )
            ->whereNotNull('counterparty_name')
            ->groupBy('counterparty_name', 'counterparty_ico')
            ->orderByDesc('total_amount')
            ->limit(self::TOP_COUNTERPARTIES_LIMIT)
            ->get();
    }
}
