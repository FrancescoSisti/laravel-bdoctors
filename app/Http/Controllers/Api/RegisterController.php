<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\HasApiTokens;

class RegisterController extends Controller
{
    /**
     * Handle a registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $validated = $this->validateRegistrationData($request);

            $user = $this->createUser($validated);

            // Revoke any existing tokens for security
            $user->tokens()->delete();

            // Generate new token
            $token = $user->createToken('auth-token')->plainTextToken;

            // Configure secure cookie options
            $cookieOptions = [
                'name' => 'token',
                'value' => $token,
                'expires' => 60 * 24, // 24 hours
                'path' => '/',
                'domain' => null,
                'secure' => config('app.env') === 'production',
                'httponly' => true,
                'samesite' => 'lax'
            ];

            Log::info('User registered successfully', ['user_id' => $user->id]);

            return $this->successResponse($user, $token)
                ->withCookie(cookie()->make(...array_values($cookieOptions)))
                ->header('Access-Control-Allow-Origin', config('cors.allowed_origins')[0]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Registration validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422)
            ->header('Access-Control-Allow-Origin', config('cors.allowed_origins')[0]);
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred'
            ], 500)
            ->header('Access-Control-Allow-Origin', config('cors.allowed_origins')[0]);
        }
    }

    /**
     * Validate registration data
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRegistrationData(Request $request)
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'home_address' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it|org|net|edu|gov)$/'
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'specialization_id' => ['required', 'exists:specializations,id'],
        ]);
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     * @throws \Exception
     */
    private function createUser(array $data)
    {
        Log::info('Attempting to create user', array_diff_key($data, ['password' => '']));

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'home_address' => $data['home_address'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!$user) {
            throw new \Exception('Failed to create user');
        }

        $user->specializations()->attach($data['specialization_id']);

        $user->load('specializations');

        return $user;
    }

    /**
     * Return success response
     *
     * @param User $user
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse(User $user, string $token)
    {
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }
}