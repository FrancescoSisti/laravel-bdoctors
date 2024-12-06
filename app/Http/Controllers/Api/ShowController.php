<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 401);
            }

            $user = User::with([
                'profile.messages',
                'profile.sponsorships',
                'specializations'
            ])->findOrFail($id);

            if (!$user->profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profilo non trovato'
                ], 404);
            }

            $profile = $user->profile;

            $responseData = [
                'id' => $profile->id,
                'curriculum' => $profile->curriculum,
                'photo' => $profile->photo,
                'office_address' => $profile->office_address,
                'phone' => $profile->phone,
                'services' => $profile->services,
                'doctor' => [
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
                ],
                'has_active_sponsorship' => $profile->sponsorships
                    ->where('pivot.end_date', '>', now())
                    ->isNotEmpty()
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utente o profilo non trovato'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Errore nel recupero del profilo', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Si è verificato un errore nel recupero del profilo'
            ], 500);
        }
    }
}
