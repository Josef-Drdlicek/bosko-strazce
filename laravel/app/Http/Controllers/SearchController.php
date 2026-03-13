<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Subsidy;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->input('q', '');

        if (blank($query)) {
            return view('search', [
                'query' => '',
                'documents' => collect(),
                'contracts' => collect(),
                'entities' => collect(),
                'subsidies' => collect(),
            ]);
        }

        return view('search', [
            'query' => $query,
            'documents' => Document::originals()->search($query)->limit(20)->get(),
            'contracts' => Contract::search($query)->limit(20)->get(),
            'entities' => Entity::search($query)->limit(20)->get(),
            'subsidies' => Subsidy::search($query)->limit(20)->get(),
        ]);
    }
}
