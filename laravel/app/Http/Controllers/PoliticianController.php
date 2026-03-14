<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Services\PoliticianService;

class PoliticianController extends Controller
{
    public function __construct(
        private readonly PoliticianService $politicianService,
    ) {}

    public function index()
    {
        $politicians = $this->politicianService->getAllPoliticians();

        $withConflicts = $politicians->filter(fn ($p) => $p->has_conflicts)->count();
        $uniqueParties = $politicians->pluck('party')->filter()->unique()->sort()->values();

        return view('politicians.index', [
            'politicians' => $politicians,
            'withConflicts' => $withConflicts,
            'uniqueParties' => $uniqueParties,
            'totalPoliticians' => $politicians->count(),
        ]);
    }

    public function show(Entity $politician)
    {
        abort_unless($politician->entity_type === 'person', 404);

        $data = $this->politicianService->getPoliticianDetail($politician);

        return view('politicians.show', $data);
    }
}
