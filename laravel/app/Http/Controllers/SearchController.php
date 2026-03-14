<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService,
    ) {}

    public function __invoke(Request $request)
    {
        $query = $request->input('q', '');
        $results = $this->searchService->search($query);

        return view('search', array_merge(['query' => $query], $results));
    }
}
