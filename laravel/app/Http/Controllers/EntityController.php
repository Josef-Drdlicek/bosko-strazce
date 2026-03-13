<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index(Request $request)
    {
        $query = Entity::query();

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('type')) {
            $query->where('entity_type', $request->input('type'));
        }

        return view('entities.index', [
            'entities' => $query->orderBy('name')->paginate(25)->withQueryString(),
            'types' => Entity::distinct()->pluck('entity_type')->sort(),
            'currentType' => $request->input('type'),
            'searchQuery' => $request->input('q'),
        ]);
    }

    public function show(Entity $entity)
    {
        $links = $entity->links()->get();

        $documentIds = $links->where('linked_type', 'document')->pluck('linked_id');
        $contractIds = $links->where('linked_type', 'contract')->pluck('linked_id');
        $subsidyIds = $links->where('linked_type', 'subsidy')->pluck('linked_id');

        return view('entities.show', [
            'entity' => $entity,
            'links' => $links,
            'documents' => Document::whereIn('id', $documentIds)->get(),
            'contracts' => Contract::whereIn('id', $contractIds)->get(),
            'subsidies' => Subsidy::whereIn('id', $subsidyIds)->get(),
        ]);
    }
}
