<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShowController extends Controller
{
    /**
     * Display the specified profile with user data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $profile = Profile::with(['user' => function($query) {
                $query->with('specializations');
            }])->findOrFail($id);

            // Combine user and profile data
            $combinedData = array_merge(
                $profile->toArray(),
                ['user_details' => $profile->user->toArray()]
            );

            Log::info('Profile and user data retrieved successfully', ['profile_id' => $id]);

            return response()->json([
                'message' => 'Profile and user data retrieved successfully',
                'data' => $combinedData
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Profile not found', ['profile_id' => $id]);
            return response()->json([
                'message' => 'Profile not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving profile and user data', [
                'profile_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Error retrieving profile and user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}