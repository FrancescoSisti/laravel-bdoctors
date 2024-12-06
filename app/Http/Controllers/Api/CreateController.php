<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateController extends Controller
{
    /**
     * Create a new profile for a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // Check if user already has a profile
            if (Profile::where('user_id', $request->user_id)->exists()) {
                Log::warning('Duplicate profile creation attempt', [
                    'user_id' => $request->user_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'User already has a profile'
                ], 409);
            }

            $validated = $this->validateProfileData($request);

            $profile = $this->createProfile($validated);

            // Load relationships for response
            $profile->load(['user.specializations']);

            // Transform the data into a consistent format
            $responseData = [
                'id' => $profile->id,
                'curriculum' => $profile->curriculum,
                'photo' => $profile->photo,
                'office_address' => $profile->office_address,
                'phone' => $profile->phone,
                'services' => $profile->services,
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

            Log::info('Profile created successfully', ['profile_id' => $profile->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile created successfully',
                'data' => $responseData
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Profile creation validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Profile creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate profile data
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateProfileData(Request $request)
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'curriculum' => ['required', 'string', 'min:200', 'max:1000'],
            'photo' => ['required', 'string', 'url'],
            'office_address' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'services' => ['required', 'string', 'min:30', 'max:100'],
        ]);
    }

    /**
     * Create a new profile
     *
     * @param array $data
     * @return Profile
     * @throws \Exception
     */
    private function createProfile(array $data)
    {
        Log::info('Attempting to create profile', $data);

        $profile = Profile::create($data);

        if (!$profile) {
            throw new \Exception('Failed to create profile');
        }

        return $profile;
    }
}