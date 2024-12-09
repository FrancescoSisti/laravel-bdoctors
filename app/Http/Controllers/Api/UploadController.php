<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $codice = $data['codice'];

        $uploadedFile = $request->file('file');
        $ext = $uploadedFile->getClientOriginalExtension();
        $fileName = $codice . date('YmdHis') . '.' . $ext;

        try {
            $path = $uploadedFile->storeAs('files', $fileName, 'local');

            // Create upload record
            $upload = Upload::create([
                'file_name' => $fileName,
                'file_path' => $path,
                'file_type' => $uploadedFile->getClientMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File caricato correttamente',
                'data' => $upload
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il caricamento del file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Upload $upload)
    {
        return response()->json([
            'success' => true,
            'data' => $upload
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Upload $upload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Upload $upload)
    {
        try {
            $upload->delete();

            return response()->json([
                'success' => true,
                'message' => 'File eliminato correttamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione del file: ' . $e->getMessage()
            ], 500);
        }
    }
}