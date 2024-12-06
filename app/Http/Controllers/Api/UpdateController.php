<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    public function update(Request $request, $id)
    {
        try {
            // Find profile and verify ownership
            $profile = Profile::with('user')->findOrFail($id);

            if ($profile->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this profile'
                ], 403);
            }

            // Validate all input data at once
            $validator = Validator::make($request->all(), [
                // Profile validation rules
                'curriculum' => 'nullable|string|max:5000',
                'photo' => 'nullable|string|max:255',
                'office_address' => 'required|string|max:255',
                'phone' => 'required|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
                'services' => 'nullable|string|max:1000',

                // User validation rules
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:50|unique:users,email,' . $profile->user->id,
                'specializations' => 'required|array|min:1',
                'specializations.*' => 'exists:specializations,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            try {
                // Update profile
                $profile->update($validator->safe()->only([
                    'curriculum',
                    'photo',
                    'office_address',
                    'phone',
                    'services'
                ]));

                // Update user
                $profile->user->update($validator->safe()->only([
                    'first_name',
                    'last_name',
                    'email'
                ]));

                // Update specializations
                $profile->user->specializations()->sync($request->specializations);

                DB::commit();

                Log::info('Profile updated successfully', [
                    'profile_id' => $id,
                    'user_id' => $profile->user_id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'data' => $profile->fresh(['user.specializations'])
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

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
