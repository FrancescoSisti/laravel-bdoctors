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
            $validated = $this->validateProfileData($request);

            $profile = $this->createProfile($validated);

            Log::info('Profile created successfully', ['profile_id' => $profile->id]);

            return response()->json([
                'message' => 'Profile created successfully',
                'profile' => $profile
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Profile creation validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
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
            'phone' => ['required', 'string', 'max:20'],
            'services' => ['required', 'string', 'min:5', 'max:100'],
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
