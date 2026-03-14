<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct(
        private readonly ContractService $contractService,
    ) {}

    public function index(Request $request)
    {
        return view('contracts.index', [
            'contracts' => $this->contractService->getFilteredPaginated($request->all()),
            'searchQuery' => $request->input('q'),
            'sortField' => $request->input('sort', 'date_signed'),
            'sortDir' => $request->input('dir', 'desc'),
        ]);
    }

    public function show(Contract $contract)
    {
        return view('contracts.show', $this->contractService->getWithRelations($contract));
    }
}
