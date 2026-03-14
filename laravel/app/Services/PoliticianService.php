<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\EntityLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PoliticianService
{
    public function getAllPoliticians(): Collection
    {
        $councilLinks = EntityLink::where('role', 'council_member')
            ->with('entity')
            ->get();

        $personIds = $councilLinks->pluck('entity_id')->unique();

        $persons = Entity::whereIn('id', $personIds)
            ->where('entity_type', 'person')
            ->get()
            ->keyBy('id');

        $companyRoles = $this->loadCompanyRolesForPersons($personIds);
        $conflictData = $this->loadConflictDataForCompanies($companyRoles);

        return $persons->map(function (Entity $person) use ($companyRoles, $conflictData) {
            $metadata = is_array($person->metadata_json) ? $person->metadata_json : [];
            $companies = $companyRoles->get($person->id, collect());

            $totalContracts = 0;
            $totalAmount = 0;

            $enrichedCompanies = $companies->map(function ($c) use ($conflictData, &$totalContracts, &$totalAmount) {
                $stats = $conflictData->get($c->company_id, (object) ['contract_count' => 0, 'total_amount' => 0]);
                $totalContracts += $stats->contract_count;
                $totalAmount += $stats->total_amount;

                return (object) [
                    'id' => $c->company_id,
                    'name' => $c->company_name,
                    'ico' => $c->company_ico,
                    'role' => $c->company_role,
                    'role_label' => EntityLink::roleLabelFor($c->company_role),
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
        })->sortByDesc('total_amount')->values();
    }

    public function getPoliticianDetail(Entity $person): array
    {
        $metadata = is_array($person->metadata_json) ? $person->metadata_json : [];

        $allElections = $this->loadAllElections($person->name);

        $companyRoles = $this->loadCompanyRolesForSinglePerson($person);
        $conflictData = $this->loadConflictDataForCompanies($companyRoles);

        $companies = $companyRoles->map(function ($c) use ($conflictData) {
            $stats = $conflictData->get($c->company_id, (object) ['contract_count' => 0, 'total_amount' => 0]);

            $topContracts = collect();
            if ($stats->contract_count > 0) {
                $topContracts = DB::table('entity_links')
                    ->join('contracts', function ($join) {
                        $join->on('entity_links.linked_id', '=', 'contracts.id')
                            ->where('entity_links.linked_type', '=', 'contract');
                    })
                    ->where('entity_links.entity_id', $c->company_id)
                    ->orderByDesc('contracts.amount')
                    ->limit(5)
                    ->select('contracts.id', 'contracts.subject', 'contracts.amount', 'contracts.date_signed')
                    ->get();
            }

            return (object) [
                'id' => $c->company_id,
                'name' => $c->company_name,
                'ico' => $c->company_ico,
                'role' => $c->company_role,
                'role_label' => EntityLink::roleLabelFor($c->company_role),
                'contract_count' => $stats->contract_count,
                'total_amount' => $stats->total_amount,
                'top_contracts' => $topContracts,
            ];
        })->sortByDesc('total_amount')->values();

        return [
            'person' => $person,
            'party' => $metadata['party'] ?? null,
            'election_year' => $metadata['election_year'] ?? null,
            'votes' => $metadata['votes'] ?? null,
            'elections' => $allElections,
            'companies' => $companies,
            'total_contracts' => $companies->sum('contract_count'),
            'total_amount' => $companies->sum('total_amount'),
        ];
    }

    private function loadAllElections(string $personName): Collection
    {
        return DB::table('entity_links as el')
            ->join('entities as person', 'person.id', '=', 'el.entity_id')
            ->where('el.role', 'council_member')
            ->whereRaw('LOWER(person.name) = ?', [mb_strtolower($personName)])
            ->select('person.id', 'person.metadata_json')
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

    private function loadCompanyRolesForPersons(Collection $personIds): Collection
    {
        $personNames = Entity::whereIn('id', $personIds)->pluck('name', 'id');

        $normalizedNames = $personNames->map(fn ($name) => mb_strtolower($name))->unique();

        $allMatches = DB::table('entity_links as sr')
            ->join('entities as person', 'person.id', '=', 'sr.entity_id')
            ->join('entities as company', 'company.id', '=', 'sr.linked_id')
            ->where('sr.linked_type', 'entity')
            ->whereIn('sr.role', ['statutory', 'chairman', 'vice_chairman', 'supervisory_member'])
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
        foreach ($personNames as $id => $name) {
            $normalized = mb_strtolower($name);
            $result[$id] = $matchByName->get($normalized, collect());
        }

        return $result;
    }

    private function loadCompanyRolesForSinglePerson(Entity $person): Collection
    {
        return DB::table('entity_links as sr')
            ->join('entities as p', 'p.id', '=', 'sr.entity_id')
            ->join('entities as company', 'company.id', '=', 'sr.linked_id')
            ->where('sr.linked_type', 'entity')
            ->whereIn('sr.role', ['statutory', 'chairman', 'vice_chairman', 'supervisory_member'])
            ->whereRaw('LOWER(p.name) = ?', [mb_strtolower($person->name)])
            ->select(
                'company.id as company_id',
                'company.name as company_name',
                'company.ico as company_ico',
                'sr.role as company_role',
            )
            ->get();
    }

    private function loadConflictDataForCompanies(Collection $companyRoles): Collection
    {
        $companyIds = $companyRoles instanceof \Illuminate\Support\Collection
            ? $companyRoles->flatten()->pluck('company_id')->filter()->unique()
            : collect();

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
}
