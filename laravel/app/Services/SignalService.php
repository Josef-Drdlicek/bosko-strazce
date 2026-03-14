<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Entity;
use App\Models\EntityLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SignalService
{
    public function getAllSignals(): array
    {
        return [
            'contractConcentration' => $this->detectContractConcentration(),
            'subsidyConcentration' => $this->detectSubsidyConcentration(),
            'highValueContracts' => $this->detectHighValueContracts(),
            'summary' => $this->getSignalSummary(),
        ];
    }

    /**
     * Entities with contract count or total amount significantly above the median.
     * Threshold: entity must have > 2× the median contract count OR total amount.
     */
    public function detectContractConcentration(int $limit = 20): Collection
    {
        $entityStats = DB::table('entity_links')
            ->join('contracts', function ($join) {
                $join->on('entity_links.linked_id', '=', 'contracts.id')
                    ->where('entity_links.linked_type', '=', 'contract');
            })
            ->join('entities', 'entity_links.entity_id', '=', 'entities.id')
            ->where('entity_links.role', 'counterparty')
            ->groupBy('entities.id', 'entities.name', 'entities.ico')
            ->select([
                'entities.id',
                'entities.name',
                'entities.ico',
                DB::raw('COUNT(DISTINCT contracts.id) as contract_count'),
                DB::raw('COALESCE(SUM(contracts.amount), 0) as total_amount'),
                DB::raw('MIN(contracts.date_signed) as first_contract'),
                DB::raw('MAX(contracts.date_signed) as last_contract'),
            ])
            ->having('contract_count', '>', 1)
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get();

        if ($entityStats->isEmpty()) {
            return collect();
        }

        $medianAmount = $this->calculateMedianContractTotal();
        $medianCount = $this->calculateMedianContractCount();

        return $entityStats->map(function ($stat) use ($medianAmount, $medianCount) {
            $amountRatio = $medianAmount > 0 ? $stat->total_amount / $medianAmount : 0;
            $countRatio = $medianCount > 0 ? $stat->contract_count / $medianCount : 0;

            return (object) [
                'entity_id' => $stat->id,
                'entity_name' => $stat->name,
                'entity_ico' => $stat->ico,
                'contract_count' => $stat->contract_count,
                'total_amount' => $stat->total_amount,
                'first_contract' => $stat->first_contract,
                'last_contract' => $stat->last_contract,
                'amount_ratio' => round($amountRatio, 1),
                'count_ratio' => round($countRatio, 1),
                'severity' => $this->calculateSeverity($amountRatio, $countRatio),
            ];
        });
    }

    public function detectSubsidyConcentration(int $limit = 20): Collection
    {
        return DB::table('entity_links')
            ->join('subsidies', function ($join) {
                $join->on('entity_links.linked_id', '=', 'subsidies.id')
                    ->where('entity_links.linked_type', '=', 'subsidy');
            })
            ->join('entities', 'entity_links.entity_id', '=', 'entities.id')
            ->groupBy('entities.id', 'entities.name', 'entities.ico')
            ->select([
                'entities.id',
                'entities.name',
                'entities.ico',
                DB::raw('COUNT(DISTINCT subsidies.id) as subsidy_count'),
                DB::raw('COALESCE(SUM(subsidies.amount), 0) as total_amount'),
                DB::raw('MIN(subsidies.year) as first_year'),
                DB::raw('MAX(subsidies.year) as last_year'),
            ])
            ->having('subsidy_count', '>', 0)
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get();
    }

    public function detectHighValueContracts(int $limit = 20): Collection
    {
        return Contract::whereNotNull('amount')
            ->where('amount', '>', 0)
            ->orderByDesc('amount')
            ->limit($limit)
            ->get();
    }

    public function getSignalSummary(): array
    {
        $totalContracts = Contract::count();
        $totalAmount = (float) Contract::sum('amount');
        $avgAmount = $totalContracts > 0 ? $totalAmount / $totalContracts : 0;

        $entitiesWithContracts = EntityLink::where('linked_type', 'contract')
            ->where('role', 'counterparty')
            ->distinct('entity_id')
            ->count('entity_id');

        return [
            'total_contracts' => $totalContracts,
            'total_amount' => $totalAmount,
            'average_amount' => round($avgAmount, 2),
            'unique_counterparties' => $entitiesWithContracts,
            'median_contract_total' => $this->calculateMedianContractTotal(),
            'median_contract_count' => $this->calculateMedianContractCount(),
        ];
    }

    private function calculateMedianContractTotal(): float
    {
        $totals = DB::table('entity_links')
            ->join('contracts', function ($join) {
                $join->on('entity_links.linked_id', '=', 'contracts.id')
                    ->where('entity_links.linked_type', '=', 'contract');
            })
            ->where('entity_links.role', 'counterparty')
            ->groupBy('entity_links.entity_id')
            ->selectRaw('COALESCE(SUM(contracts.amount), 0) as total')
            ->pluck('total')
            ->sort()
            ->values();

        return $this->median($totals);
    }

    private function calculateMedianContractCount(): float
    {
        $counts = DB::table('entity_links')
            ->where('linked_type', 'contract')
            ->where('role', 'counterparty')
            ->groupBy('entity_id')
            ->selectRaw('COUNT(*) as cnt')
            ->pluck('cnt')
            ->sort()
            ->values();

        return $this->median($counts);
    }

    private function median(Collection $sorted): float
    {
        if ($sorted->isEmpty()) {
            return 0;
        }

        $count = $sorted->count();
        $middle = intdiv($count, 2);

        if ($count % 2 === 0) {
            return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        }

        return (float) $sorted[$middle];
    }

    private function calculateSeverity(float $amountRatio, float $countRatio): string
    {
        $maxRatio = max($amountRatio, $countRatio);

        if ($maxRatio >= 10) {
            return 'high';
        }

        if ($maxRatio >= 4) {
            return 'medium';
        }

        return 'low';
    }
}
