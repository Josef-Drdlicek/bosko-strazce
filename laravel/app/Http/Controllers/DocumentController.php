<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Entity;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::originals()->with('attachments');

        if ($request->filled('section')) {
            $query->section($request->input('section'));
        }

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        return view('documents.index', [
            'documents' => $query->latest('published_date')->paginate(25)->withQueryString(),
            'sections' => Document::originals()->distinct()->pluck('section')->sort(),
            'currentSection' => $request->input('section'),
            'searchQuery' => $request->input('q'),
        ]);
    }

    public function show(Document $document)
    {
        $document->load('attachments', 'duplicateOf', 'duplicates');

        $linkedEntities = Entity::whereHas('links', function ($query) use ($document) {
            $query->where('linked_type', 'document')
                ->where('linked_id', $document->id);
        })->get();

        return view('documents.show', [
            'document' => $document,
            'linkedEntities' => $linkedEntities,
        ]);
    }
}
