<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\EntityLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PoliticianService
{
    private const STATUTORY_ROLES = ['statutory', 'chairman', 'vice_chairman', 'supervisory_member'];
    private const TOP_CONTRACTS_LIMIT = 5;

    public function getAllPoliticians(): Collection
    {
        $personIds = EntityLink::where('role', 'council_member')
            ->pluck('entity_id')
            ->unique();

        $persons = Entity::whereIn('id', $personIds)
            ->where('entity_type', 'person')
            ->get()
            ->keyBy('id');

        $companyRoles = $this->loadCompanyRolesForPersons($persons);
        $companyIds = $this->extractCompanyIds($companyRoles);
        $conflictData = $this->loadContractStatsForCompanies($companyIds);

        return $persons->map(function (Entity $person) use ($companyRoles, $conflictData) {
            return $this->buildPoliticianSummary($person, $companyRoles, $conflictData);
        })->sortByDesc('total_amount')->values();
    }

    public function getPoliticianDetail(Entity $person): array
    {
        $metadata = is_array($person->metadata_json) ? $person->metadata_json : [];

        $elections = $this->loadAllElections($person->name);
        $companyRoles = $this->loadCompanyRolesForSinglePerson($person);
        $companyIds = $companyRoles->pluck('company_id')->filter()->unique();
        $conflictData = $this->loadContractStatsForCompanies($companyIds);

        $companies = $this->buildCompanyDetails($companyRoles, $conflictData);

        return [
            'person' => $person,
            'party' => $metadata['party'] ?? null,
            'election_year' => $metadata['election_year'] ?? null,
            'votes' => $metadata['votes'] ?? null,
            'elections' => $elections,
            'companies' => $companies,
            'total_contracts' => $companies->sum('contract_count'),
            'total_amount' => $companies->sum('total_amount'),
        ];
    }

    private function buildPoliticianSummary(
        Entity $person,
        Collection $companyRoles,
        Collection $conflictData,
    ): object {
        $metadata = is_array($person->metadata_json) ? $person->metadata_json : [];
        $companies = $companyRoles->get($person->id, collect());

        $totalContracts = 0;
        $totalAmount = 0;

        $enrichedCompanies = $companies->map(function ($company) use ($conflictData, &$totalContracts, &$totalAmount) {
            $stats = $conflictData->get($company->company_id, (object) ['contract_count' => 0, 'total_amount' => 0]);
            $totalContracts += $stats->contract_count;
            $totalAmount += $stats->total_amount;

            return (object) [
                'id' => $company->company_id,
                'name' => $company->company_name,
                'ico' => $company->company_ico,
                'role' => $company->company_role,
                'role_label' => EntityLink::roleLabelFor($company->company_role),
                'contract_count' => $stats->contract_count,
                'total_amount' => $stats->total_amount,
            ];
        });

        return (object) [
            'id' => $person->id,
            'name' => $person->name,
            'party' => $metadata['party'] ?? null,
            'election_year' => $metadata['election_year'] ?? null,
            'votes' => $metadata['votes'] ?? null,
            'companies' => $enrichedCompanies,
            'company_count' => $enrichedCompanies->count(),
            'has_conflicts' => $enrichedCompanies->contains(fn ($c) => $c->contract_count > 0),
            'total_contracts' => $totalContracts,
            'total_amount' => $totalAmount,
        ];
    }

    private function buildCompanyDetails(Collection $companyRoles, Collection $conflictData): Collection
    {
        return $companyRoles->map(function ($company) use ($conflictData) {
            $stats = $conflictData->get($company->company_id, (object) ['contract_count' => 0, 'total_amount' => 0]);

            $topContracts = $stats->contract_count > 0
                ? $this->loadTopContracts($company->company_id)
                : collect();

            return (object) [
                'id' => $company->company_id,
                'name' => $company->company_name,
                'ico' => $company->company_ico,
                'role' => $company->company_role,
                'role_label' => EntityLink::roleLabelFor($company->company_role),
                'contract_count' => $stats->contract_count,
                'total_amount' => $stats->total_amount,
                'top_contracts' => $topContracts,
            ];
        })->sortByDesc('total_amount')->values();
    }

    private function loadAllElections(string $personName): Collection
    {
        return DB::table('entity_links as el')
            ->join('entities as person', 'person.id', '=', 'el.entity_id')
            ->where('el.role', 'council_member')
            ->whereRaw('LOWER(person.name) = ?', [mb_strtolower($personName)])
            ->select('person.metadata_json')
            ->get()
            ->map(function ($row) {
                $meta = json_decode($row->metadata_json, true) ?? [];

                return (object) [
                    'year' => $meta['election_year'] ?? null,
                    'party' => $meta['party'] ?? null,
                    'votes' => $meta['votes'] ?? null,
                ];
            })
            ->filter(fn ($e) => $e->year !== null)
            ->unique('year')
            ->sortByDesc('year')
            ->values();
    }

    private function loadCompanyRolesForPersons(Collection $persons): Collection
    {
        $normalizedNames = $persons->map(fn (Entity $p) => mb_strtolower($p->name))->unique();

        $allMatches = DB::table('entity_links as sr')
            ->join('entities as person', 'person.id', '=', 'sr.entity_id')
            ->join('entities as company', 'company.id', '=', 'sr.linked_id')
            ->where('sr.linked_type', 'entity')
            ->whereIn('sr.role', self::STATUTORY_ROLES)
            ->select(
                'person.name as person_name',
                'company.id as company_id',
                'company.name as company_name',
                'company.ico as company_ico',
                'sr.role as company_role',
            )
            ->get();

        $matchByName = $allMatches->groupBy(fn ($m) => mb_strtolower($m->person_name));

        $result = collect();
        foreach ($persons as $person) {
            $normalized = mb_strtolower($person->name);
            $result[$person->id] = $matchByName->get($normalized, collect());
        }

        return $result;
    }

    private function loadCompanyRolesForSinglePerson(Entity $person): Collection
    {
        return DB::table('entity_links as sr')
            ->join('entities as p', 'p.id', '=', 'sr.entity_id')
            ->join('entities as company', 'company.id', '=', 'sr.linked_id')
            ->where('sr.linked_type', 'entity')
            ->whereIn('sr.role', self::STATUTORY_ROLES)
            ->whereRaw('LOWER(p.name) = ?', [mb_strtolower($person->name)])
            ->select(
                'company.id as company_id',
                'company.name as company_name',
                'company.ico as company_ico',
                'sr.role as company_role',
            )
            ->get();
    }

    private function extractCompanyIds(Collection $companyRoles): Collection
    {
        return $companyRoles->flatten()->pluck('company_id')->filter()->unique();
    }

    private function loadContractStatsForCompanies(Collection $companyIds): Collection
    {
        if ($companyIds->isEmpty()) {
            return collect();
        }

        return DB::table('entity_links')
            ->join('contracts', function ($join) {
                $join->on('entity_links.linked_id', '=', 'contracts.id')
                    ->where('entity_links.linked_type', '=', 'contract');
            })
            ->whereIn('entity_links.entity_id', $companyIds)
            ->groupBy('entity_links.entity_id')
            ->select(
                'entity_links.entity_id as company_id',
                DB::raw('COUNT(DISTINCT contracts.id) as contract_count'),
                DB::raw('COALESCE(SUM(contracts.amount), 0) as total_amount'),
            )
            ->get()
            ->keyBy('company_id');
    }

    private function loadTopContracts(int $companyId): Collection
    {
        return DB::table('entity_links')
            ->join('contracts', function ($join) {
                $join->on('entity_links.linked_id', '=', 'contracts.id')
                    ->where('entity_links.linked_type', '=', 'contract');
            })
            ->where('entity_links.entity_id', $companyId)
            ->orderByDesc('contracts.amount')
            ->limit(self::TOP_CONTRACTS_LIMIT)
            ->select('contracts.id', 'contracts.subject', 'contracts.amount', 'contracts.date_signed')
            ->get();
    }
}
