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
            // Find profile and verify ownership
            $profile = Profile::with(['user.specializations'])
                ->findOrFail($id);

            if ($profile->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to edit this profile'
                ], 403);
            }

            // Transform the data with default values for null fields
            $responseData = [
                'id' => $profile->id,
                'curriculum' => $profile->curriculum ?? '',
                'photo' => $profile->photo ?? '',
                'office_address' => $profile->office_address ?? '',
                'phone' => $profile->phone ?? '',
                'services' => $profile->services ?? '',
                'doctor' => [
                    'id' => $profile->user->id,
                    'first_name' => $profile->user->first_name ?? '',
                    'last_name' => $profile->user->last_name ?? '',
                    'email' => $profile->user->email ?? '',
                    'specializations' => $profile->user->specializations->map(function($spec) {
                        return [
                            'id' => $spec->id ?? 0,
                            'name' => $spec->name ?? ''
                        ];
                    })->values()->all()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData
            ], 200);

        } catch (\Exception $e) {
            Log::error('Edit profile error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Method intentionally left empty as it exists in UpdateController
    }
}