<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;

class SearchService
{
    private const RESULTS_LIMIT = 20;

    public function search(string $query): array
    {
        if (blank($query)) {
            return $this->emptyResults();
        }

        return [
            'documents' => Document::originals()->search($query)->limit(self::RESULTS_LIMIT)->get(),
            'contracts' => Contract::search($query)->limit(self::RESULTS_LIMIT)->get(),
            'entities' => Entity::search($query)->limit(self::RESULTS_LIMIT)->get(),
            'subsidies' => Subsidy::search($query)->limit(self::RESULTS_LIMIT)->get(),
        ];
    }

    public function hasResults(array $results): bool
    {
        foreach ($results as $items) {
            if ($items->isNotEmpty()) {
                return true;
            }
        }

        return false;
    }

    private function emptyResults(): array
    {
        return [
            'documents' => collect(),
            'contracts' => collect(),
            'entities' => collect(),
            'subsidies' => collect(),
        ];
    }
}
