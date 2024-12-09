<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Support\Facades\Log;

class SpecializationController extends Controller
{
    public function index()
    {
        try {
            $specializations = Specialization::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $specializations
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching specializations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching specializations',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
