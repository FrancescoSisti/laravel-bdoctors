<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * Create a new profile for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'curriculum' => 'nullable|string',
            'photo' => 'nullable|string',
            'office_address' => 'required|string',
            'phone' => 'required|string',
            'services' => 'required|string'
        ]);

        // Get authenticated user
        $user = Auth::user();

        // Create profile
        $profile = Profile::create([
            'user_id' => $user->id,
            'curriculum' => $validated['curriculum'],
            'photo' => $validated['photo'],
            'office_address' => $validated['office_address'],
            'phone' => $validated['phone'],
            'services' => $validated['services']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile created successfully',
            'data' => $profile
        ], 201);
    }
}