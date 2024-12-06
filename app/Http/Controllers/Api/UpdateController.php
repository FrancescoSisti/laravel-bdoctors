<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    public function update(Request $request, string $id)
    {
        try {
            $validated = $this->validateProfileData($request);

            $profile = Profile::findOrFail($id);


            $profile->phone = $validated['phone'];
            $profile->office_address = $validated['office_address'];
            $profile->services = $validated['services'];
            $profile->photo = $validated['photo'];
            $profile->curriculum = $validated['curriculum'];

            $profile->save();

            Log::info('Profile updated successfully', ['profile_id' => $profile->id]);

            return response()->json([
                'message' => 'Profile updated successfully',
                'profile' => $profile
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Profile update validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Updating Profile failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Update Profile failed',
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
}
