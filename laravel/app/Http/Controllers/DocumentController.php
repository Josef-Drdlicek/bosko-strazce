<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    public function index(Request $request)
    {
        return view('documents.index', [
            'documents' => $this->documentService->getFilteredPaginated($request->all()),
            'sections' => $this->documentService->getAvailableSections(),
            'currentSection' => $request->input('section'),
            'searchQuery' => $request->input('q'),
        ]);
    }

    public function show(Document $document)
    {
        return view('documents.show', $this->documentService->getWithRelations($document));
    }
}
