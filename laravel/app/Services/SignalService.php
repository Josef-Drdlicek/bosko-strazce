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
            'conflictsOfInterest' => $this->detectConflictsOfInterest(),
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

    /**
     * People who are both city council members AND hold statutory roles in companies
     * that have contracts with the city.
     *
     * Cross-references by normalized name because the same person may have separate
     * entity records from different sources (volby.cz vs ARES VR).
     */
    public function detectConflictsOfInterest(): Collection
    {
        $councilMembers = DB::table('entity_links as cm')
            ->join('entities as person', 'person.id', '=', 'cm.entity_id')
            ->where('cm.role', 'council_member')
            ->where('person.entity_type', 'person')
            ->select('person.id as person_id', 'person.name as person_name')
            ->distinct()
            ->get();

        $statutoryPersons = DB::table('entity_links as sr')
            ->join('entities as person', 'person.id', '=', 'sr.entity_id')
            ->join('entities as company', 'company.id', '=', 'sr.linked_id')
            ->where('sr.linked_type', 'entity')
            ->whereIn('sr.role', ['statutory', 'chairman', 'vice_chairman', 'supervisory_member'])
            ->select(
                'person.id as person_id',
                'person.name as person_name',
                'company.id as company_id',
                'company.name as company_name',
                'company.ico as company_ico',
                'sr.role as company_role',
            )
            ->get();

        $statutoryByName = $statutoryPersons->groupBy(
            fn ($item) => mb_strtolower($item->person_name),
        );

        $conflicts = collect();

        foreach ($councilMembers as $member) {
            $normalizedName = mb_strtolower($member->person_name);
            $matches = $statutoryByName->get($normalizedName, collect());

            foreach ($matches as $match) {
                $contractStats = DB::table('entity_links')
                    ->join('contracts', function ($join) {
                        $join->on('entity_links.linked_id', '=', 'contracts.id')
                            ->where('entity_links.linked_type', '=', 'contract');
                    })
                    ->where('entity_links.entity_id', $match->company_id)
                    ->select(
                        DB::raw('COUNT(DISTINCT contracts.id) as contract_count'),
                        DB::raw('COALESCE(SUM(contracts.amount), 0) as total_amount'),
                    )
                    ->first();

                $conflicts->push((object) [
                    'person_id' => $member->person_id,
                    'person_name' => $member->person_name,
                    'company_id' => $match->company_id,
                    'company_name' => $match->company_name,
                    'company_ico' => $match->company_ico,
                    'company_role' => $match->company_role,
                    'company_role_label' => EntityLink::roleLabelFor($match->company_role),
                    'contract_count' => $contractStats->contract_count ?? 0,
                    'total_amount' => $contractStats->total_amount ?? 0,
                    'severity' => ($contractStats->contract_count ?? 0) > 0 ? 'high' : 'low',
                ]);
            }
        }

        return $conflicts->sortByDesc('total_amount')->values();
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
