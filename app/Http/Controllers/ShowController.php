<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShowController extends Controller
{
    /**
     * Display the specified profile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $profile = Profile::with('user')->findOrFail($id);

            Log::info('Profile retrieved successfully', ['profile_id' => $id]);

            return response()->json([
                'message' => 'Profile retrieved successfully',
                'profile' => $profile
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Profile not found', ['profile_id' => $id]);
            return response()->json([
                'message' => 'Profile not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving profile', [
                'profile_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Error retrieving profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
