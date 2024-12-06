<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get profile data for editing
     *
     * @param int $id Profile ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            // Load profile with user and specializations in a single query
            $profile = Profile::with(['user.specializations'])
                ->findOrFail($id);

            // Verify ownership
            if ($profile->user_id !== auth()->id()) {
                Log::warning('Unauthorized profile edit attempt', [
                    'profile_id' => $profile->id,
                    'user_id' => auth()->id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to edit this profile'
                ], 403);
            }

            // Transform data for response
            $responseData = [
                'id' => $profile->id,
                'curriculum' => $profile->curriculum ?? '',
                'photo' => substr($profile->photo ?? '', 0, 255), // Ensure photo doesn't exceed 255 chars
                'office_address' => $profile->office_address ?? '',
                'phone' => $profile->phone ?? '',
                'services' => $profile->services ?? '',
                'user' => [
                    'id' => $profile->user->id,
                    'first_name' => $profile->user->first_name,
                    'last_name' => $profile->user->last_name,
                    'email' => $profile->user->email,
                    'specializations' => $profile->user->specializations->map(function($spec) {
                        return [
                            'id' => $spec->id,
                            'name' => $spec->name
                        ];
                    })->values()->all()
                ]
            ];

            Log::info('Profile retrieved for editing', ['profile_id' => $profile->id]);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Profile not found for editing', ['profile_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve profile for editing', [
                'profile_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the profile'
            ], 500);
        }
    }
}
