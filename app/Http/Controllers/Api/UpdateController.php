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
            $profile = Profile::with(['user', 'user.specializations'])->findOrFail($id);

            if ($profile->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato ad aggiornare questo profilo'
                ], 403);
            }

            // Validate all input data at once
            $validator = Validator::make($request->all(), [
                // Profile validation rules
                'curriculum' => 'nullable|file|mimes:pdf|max:5000',
                'photo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
                'office_address' => 'required|string|max:255',
                'phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
                'services' => 'nullable|string|max:1000',

                // User validation rules
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:50',
                    'unique:users,email,' . $profile->user->id,
                    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it|org|net|edu|gov)$/'
                ],
                'specialization_id' => 'required|numeric|exists:specializations,id'
            ], [
                'required' => 'Il campo :attribute è obbligatorio.',
                'string' => 'Il campo :attribute deve essere una stringa.',
                'max' => 'Il campo :attribute non può superare :max caratteri.',
                'email' => 'Il campo :attribute deve essere un indirizzo email valido.',
                'unique' => 'Questa email è già stata registrata.',
                'regex' => 'Il formato del campo :attribute non è valido.',
                'exists' => 'La specializzazione selezionata non è valida.',
                'phone.regex' => 'Il numero di telefono non è in un formato valido.',
                'numeric' => 'Il campo :attribute deve essere un numero.',
                'specialization_id.exists' => 'La specializzazione selezionata non esiste nel database.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errori di validazione',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $validatedData = $validator->validated();

            DB::beginTransaction();
            try {
                // Update profile
                $profile->update([
                    'curriculum' => $validatedData['curriculum'] ?? $profile->curriculum,
                    'photo' => $validatedData['photo'] ?? $profile->photo,
                    'office_address' => $validatedData['office_address'],
                    'phone' => $validatedData['phone'],
                    'services' => $validatedData['services'] ?? $profile->services
                ]);

                // Update user
                $profile->user->update([
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'email' => $validatedData['email']
                ]);

                // Update specialization
                $profile->user->specializations()->sync([(int)$validatedData['specialization_id']]);

                DB::commit();

                // Reload the model with fresh data and relationships
                $profile->load(['user', 'user.specializations']);

                return response()->json([
                    'success' => true,
                    'message' => 'Profilo aggiornato con successo',
                    'data' => $profile
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Errore durante l\'aggiornamento del profilo', [
                    'profile_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Si è verificato un errore durante l\'aggiornamento del profilo',
                    'error' => $e->getMessage()
                ], 500);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profilo non trovato'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Errore durante l\'aggiornamento del profilo', [
                'profile_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Si è verificato un errore durante l\'aggiornamento del profilo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}