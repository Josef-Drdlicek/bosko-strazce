<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Entity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DocumentService
{
    public function getFilteredPaginated(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = Document::originals()->with('attachments');

        if (!empty($filters['section'])) {
            $query->section($filters['section']);
        }

        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        return $query->latest('published_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAvailableSections(): Collection
    {
        return Document::originals()
            ->distinct()
            ->pluck('section')
            ->sort()
            ->values();
    }

    public function getWithRelations(Document $document): array
    {
        $document->load('attachments', 'duplicateOf', 'duplicates');

        $linkedEntities = Entity::whereHas('links', function ($query) use ($document) {
            $query->where('linked_type', 'document')
                ->where('linked_id', $document->id);
        })->get();

        return [
            'document' => $document,
            'linkedEntities' => $linkedEntities,
        ];
    }
}
