<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Entity;
use App\Models\EntityLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SignalService
{
    private const SEVERITY_HIGH_THRESHOLD = 10;
    private const SEVERITY_MEDIUM_THRESHOLD = 4;
    private const SEQUENCE_HIGH_AMOUNT = 5_000_000;
    private const SEQUENCE_MEDIUM_AMOUNT = 1_000_000;
    private const SEQUENCE_YEAR_WINDOW = 1;
    private const STATUTORY_ROLES = ['statutory', 'chairman', 'vice_chairman', 'supervisory_member'];

    public function getAllSignals(): array
    {
        return [
            'contractConcentration' => $this->detectContractConcentration(),
            'subsidyConcentration' => $this->detectSubsidyConcentration(),
            'highValueContracts' => $this->detectHighValueContracts(),
            'conflictsOfInterest' => $this->detectConflictsOfInterest(),
            'temporalSequences' => $this->detectTemporalSequences(),
            'summary' => $this->getSignalSummary(),
        ];
    }

    public function detectContractConcentration(int $limit = 20): Collection
    {
        $entityStats = $this->queryCounterpartyStats($limit);

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
                'severity' => $this->concentrationSeverity($amountRatio, $countRatio),
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

    public function detectConflictsOfInterest(): Collection
    {
        $councilMembers = $this->loadCouncilMembers();
        $statutoryPersons = $this->loadStatutoryPersons();
        $statutoryByName = $statutoryPersons->groupBy(fn ($item) => mb_strtolower($item->person_name));

        $conflicts = collect();

        foreach ($councilMembers as $member) {
            $matches = $statutoryByName->get(mb_strtolower($member->person_name), collect());

            foreach ($matches as $match) {
                $conflicts->push($this->buildConflictRecord($member, $match));
            }
        }

        return $conflicts->sortByDesc('total_amount')->values();
    }

    public function detectTemporalSequences(int $limit = 20): Collection
    {
        $contractRows = $this->loadEntityContractDates();
        $subsidyRows = $this->loadEntitySubsidyDates();
        $subsidiesByEntity = $subsidyRows->groupBy('entity_id');

        $sequences = collect();

        foreach ($contractRows as $contractRow) {
            $entitySubsidies = $subsidiesByEntity->get($contractRow->entity_id);
            if ($entitySubsidies === null) {
                continue;
            }

            $contractYear = (int) substr($contractRow->date_signed, 0, 4);

            foreach ($entitySubsidies as $subsidyRow) {
                if (abs($contractYear - $subsidyRow->subsidy_year) > self::SEQUENCE_YEAR_WINDOW) {
                    continue;
                }

                $key = "{$contractRow->entity_id}-{$contractRow->contract_id}-{$subsidyRow->subsidy_id}";
                if ($sequences->has($key)) {
                    continue;
                }

                $sequences[$key] = (object) [
                    'entity_id' => $contractRow->entity_id,
                    'entity_name' => $contractRow->entity_name,
                    'entity_ico' => $contractRow->entity_ico,
                    'contract_id' => $contractRow->contract_id,
                    'contract_subject' => $contractRow->contract_subject,
                    'contract_amount' => $contractRow->contract_amount,
                    'contract_date' => $contractRow->date_signed,
                    'contract_role' => $contractRow->contract_role,
                    'subsidy_id' => $subsidyRow->subsidy_id,
                    'subsidy_title' => $subsidyRow->subsidy_title,
                    'subsidy_amount' => $subsidyRow->subsidy_amount,
                    'subsidy_year' => $subsidyRow->subsidy_year,
                    'combined_amount' => $contractRow->contract_amount + $subsidyRow->subsidy_amount,
                    'severity' => $this->sequenceSeverity(
                        $contractRow->contract_amount + $subsidyRow->subsidy_amount,
                    ),
                ];
            }
        }

        return $sequences->sortByDesc('combined_amount')->values()->take($limit);
    }

    public function getSignalSummary(): array
    {
        $totalContracts = Contract::count();
        $totalAmount = (float) Contract::sum('amount');

        $entitiesWithContracts = EntityLink::where('linked_type', 'contract')
            ->where('role', 'counterparty')
            ->distinct('entity_id')
            ->count('entity_id');

        return [
            'total_contracts' => $totalContracts,
            'total_amount' => $totalAmount,
            'average_amount' => $totalContracts > 0 ? round($totalAmount / $totalContracts, 2) : 0,
            'unique_counterparties' => $entitiesWithContracts,
            'median_contract_total' => $this->calculateMedianContractTotal(),
            'median_contract_count' => $this->calculateMedianContractCount(),
        ];
    }

    private function queryCounterpartyStats(int $limit): Collection
    {
        return DB::table('entity_links')
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
    }

    private function loadCouncilMembers(): Collection
    {
        return DB::table('entity_links as cm')
            ->join('entities as person', 'person.id', '=', 'cm.entity_id')
            ->where('cm.role', 'council_member')
            ->where('person.entity_type', 'person')
            ->select('person.id as person_id', 'person.name as person_name')
            ->distinct()
            ->get();
    }

    private function loadStatutoryPersons(): Collection
    {
        return DB::table('entity_links as sr')
            ->join('entities as person', 'person.id', '=', 'sr.entity_id')
            ->join('entities as company', 'company.id', '=', 'sr.linked_id')
            ->where('sr.linked_type', 'entity')
            ->whereIn('sr.role', self::STATUTORY_ROLES)
            ->select(
                'person.id as person_id',
                'person.name as person_name',
                'company.id as company_id',
                'company.name as company_name',
                'company.ico as company_ico',
                'sr.role as company_role',
            )
            ->get();
    }

    private function buildConflictRecord(object $member, object $match): object
    {
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

        return (object) [
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
        ];
    }

    private function loadEntityContractDates(): Collection
    {
        return DB::table('entity_links as el')
            ->join('contracts as c', function ($join) {
                $join->on('el.linked_id', '=', 'c.id')
                    ->where('el.linked_type', '=', 'contract');
            })
            ->join('entities as e', 'el.entity_id', '=', 'e.id')
            ->whereNotNull('c.date_signed')
            ->where('c.amount', '>', 0)
            ->select(
                'e.id as entity_id',
                'e.name as entity_name',
                'e.ico as entity_ico',
                'c.id as contract_id',
                'c.subject as contract_subject',
                'c.amount as contract_amount',
                'c.date_signed',
                'el.role as contract_role',
            )
            ->get();
    }

    private function loadEntitySubsidyDates(): Collection
    {
        return DB::table('entity_links as el')
            ->join('subsidies as s', function ($join) {
                $join->on('el.linked_id', '=', 's.id')
                    ->where('el.linked_type', '=', 'subsidy');
            })
            ->join('entities as e', 'el.entity_id', '=', 'e.id')
            ->whereNotNull('s.year')
            ->where('s.amount', '>', 0)
            ->select(
                'e.id as entity_id',
                's.id as subsidy_id',
                's.title as subsidy_title',
                's.amount as subsidy_amount',
                's.year as subsidy_year',
            )
            ->get();
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

    private function concentrationSeverity(float $amountRatio, float $countRatio): string
    {
        $maxRatio = max($amountRatio, $countRatio);

        if ($maxRatio >= self::SEVERITY_HIGH_THRESHOLD) {
            return 'high';
        }

        if ($maxRatio >= self::SEVERITY_MEDIUM_THRESHOLD) {
            return 'medium';
        }

        return 'low';
    }

    private function sequenceSeverity(float $combinedAmount): string
    {
        if ($combinedAmount >= self::SEQUENCE_HIGH_AMOUNT) {
            return 'high';
        }

        if ($combinedAmount >= self::SEQUENCE_MEDIUM_AMOUNT) {
            return 'medium';
        }

        return 'low';
    }
}
