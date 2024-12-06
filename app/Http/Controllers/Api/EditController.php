<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class EditController extends Controller
{
    public function edit($id)
    {
        try {
            // Load profile with user and specializations in a single query
            $profile = Profile::with(['user.specializations', 'messages', 'sponsorships'])
                ->findOrFail($id);

            // Transform the data into a cleaner format
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
                    })
                ],
                'has_active_sponsorship' => $profile->sponsorships->where('pivot.end_date', '>', now())->isNotEmpty()
            ];

            Log::info('Profile retrieved successfully', ['profile_id' => $id]);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Profile not found', ['profile_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'The requested profile could not be found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve profile', [
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

    public function update(Request $request, $id)
    {
        try {
            $profile = Profile::with('user')->findOrFail($id);

            // Validate profile data
            $profileValidator = Validator::make($request->all(), [
                'curriculum' => 'nullable|string',
                'photo' => 'nullable|string',
                'office_address' => 'required|string',
                'phone' => 'required|string',
                'services' => 'nullable|string',
            ]);

            // Validate user data
            $userValidator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $profile->user->id,
                'specializations' => 'required|array',
                'specializations.*' => 'exists:specializations,id'
            ]);

            if ($profileValidator->fails() || $userValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => array_merge(
                        $profileValidator->errors()->toArray(),
                        $userValidator->errors()->toArray()
                    )
                ], 422);
            }

            // Update profile data
            $profile->update([
                'curriculum' => $request->curriculum,
                'photo' => $request->photo,
                'office_address' => $request->office_address,
                'phone' => $request->phone,
                'services' => $request->services,
            ]);

            // Update user data
            $profile->user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
            ]);

            // Sync specializations
            $profile->user->specializations()->sync($request->specializations);

            Log::info('Profile and user updated successfully', ['profile_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Profile not found for update', ['profile_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update profile', [
                'profile_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the profile'
            ], 500);
        }
    }
}