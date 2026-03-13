<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Entity;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::query();

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        $sortField = $request->input('sort', 'date_signed');
        $sortDir = $request->input('dir', 'desc');

        return view('contracts.index', [
            'contracts' => $query->orderBy($sortField, $sortDir)->paginate(25)->withQueryString(),
            'searchQuery' => $request->input('q'),
            'sortField' => $sortField,
            'sortDir' => $sortDir,
        ]);
    }

    public function show(Contract $contract)
    {
        $linkedEntities = Entity::whereHas('links', function ($query) use ($contract) {
            $query->where('linked_type', 'contract')
                ->where('linked_id', $contract->id);
        })->get();

        return view('contracts.show', [
            'contract' => $contract,
            'linkedEntities' => $linkedEntities,
        ]);
    }
}
