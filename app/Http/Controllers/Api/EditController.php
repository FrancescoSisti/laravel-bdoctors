<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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
            $this->verifyOwnership($profile);

            // Transform and return the data
            return $this->prepareSuccessResponse($profile);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->handleNotFoundError($id);
        } catch (\Exception $e) {
            return $this->handleGeneralError($id, $e);
        }
    }

    /**
     * Verify if the authenticated user owns the profile
     *
     * @param Profile $profile
     * @return void
     * @throws \Exception
     */
    private function verifyOwnership(Profile $profile)
    {
        if ($profile->user_id !== auth()->id()) {
            Log::warning('Unauthorized profile edit attempt', [
                'profile_id' => $profile->id,
                'user_id' => auth()->id()
            ]);
            abort(403, 'Unauthorized to edit this profile');
        }
    }

    /**
     * Prepare success response with transformed data
     *
     * @param Profile $profile
     * @return \Illuminate\Http\JsonResponse
     */
    private function prepareSuccessResponse(Profile $profile)
    {
        $responseData = [
            'id' => $profile->id,
            'curriculum' => $profile->curriculum ?? '',
            'photo' => $profile->photo ?? '',
            'office_address' => $profile->office_address ?? '',
            'phone' => $profile->phone ?? '',
            'services' => $profile->services ?? '',
            'doctor' => [
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
        ], 200);
    }

    /**
     * Handle not found error
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleNotFoundError($id)
    {
        Log::warning('Profile not found for editing', ['profile_id' => $id]);
        return response()->json([
            'success' => false,
            'message' => 'Profile not found'
        ], 404);
    }

    /**
     * Handle general error
     *
     * @param int $id
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleGeneralError($id, \Exception $e)
    {
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
