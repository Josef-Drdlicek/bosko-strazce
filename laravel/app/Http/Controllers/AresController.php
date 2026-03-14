<?php

namespace App\Http\Controllers;

use App\Services\AresService;
use Illuminate\Http\Request;

class AresController extends Controller
{
    public function __construct(
        private readonly AresService $aresService,
    ) {}

    public function index()
    {
        return view('ares.index', [
            'results' => [],
            'query' => '',
            'searchType' => 'name',
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $searchType = $request->input('type', 'name');
        $results = [];

        if (filled($query)) {
            $results = $searchType === 'ico'
                ? array_filter([$this->aresService->findByIco($query)])
                : $this->aresService->search($query);
        }

        return view('ares.index', [
            'results' => $results,
            'query' => $query,
            'searchType' => $searchType,
        ]);
    }
}
