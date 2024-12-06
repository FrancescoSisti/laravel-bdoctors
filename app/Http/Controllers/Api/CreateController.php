<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Check if user already has a profile
            $user = User::with('profile', 'specializations')->findOrFail(Auth::id());

            if ($user->profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has a profile'
                ], 400);
            }

            if ($user->specializations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must have at least one specialization'
                ], 400);
            }

            // Validate request data
            $validated = $request->validate([
                'curriculum' => 'nullable|string|max:5000',
                'photo' => 'nullable|string|max:255',
                'office_address' => 'required|string|max:255',
                'phone' => 'required|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
                'services' => 'required|string|max:1000'
            ]);

            // Create profile
            $profile = Profile::create([
                'user_id' => $user->id,
                'curriculum' => $validated['curriculum'],
                'photo' => $validated['photo'],
                'office_address' => $validated['office_address'],
                'phone' => $validated['phone'],
                'services' => $validated['services']
            ]);

            // Load relationships for response
            $profile->load('user.specializations');

            Log::info('Profile created successfully', ['user_id' => $user->id, 'profile_id' => $profile->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile created successfully',
                'data' => $profile
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create profile', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data needed for profile creation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData()
    {
        try {
            $user = User::with('specializations')->findOrFail(Auth::id());

            // Check if user already has a profile
            if ($user->profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has a profile'
                ], 400);
            }

            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'specializations' => $user->specializations->map(function($spec) {
                        return [
                            'id' => $spec->id,
                            'name' => $spec->name
                        ];
                    })->values()->all()
                ]
            ];

            Log::info('Profile creation data retrieved successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve profile creation data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving profile creation data'
            ], 500);
        }
    }
}