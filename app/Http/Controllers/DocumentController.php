<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $documents = $user->documents;

        return response()->json(['documents' => $documents], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return response()->json(['document' => $document], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $oldFilePath = public_path($document->path);

        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }
        $userFolder = 'uploads/' . request()->user()->id;
        $newFileName = time() . '.' . $request->file('document')->getClientOriginalExtension();
        $request->file('document')->move($userFolder, $newFileName);
        if (!($document->extension == strtolower($request->file('document')->getClientOriginalExtension())))
            return response()->json(['message' => "Document must be $document->extension format "], 400);
        $document->path = $newFileName;
        $document->extension = strtolower($request->file('document')->getClientOriginalExtension());
        $document->save();

        return response()->json(['message' => 'Document updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        if ($document->delete())
            return response()->json(['message' => 'document deleted successfully'], 200);
        return response()->json(['message' => 'Something went wrong'], 400);
    }
}
