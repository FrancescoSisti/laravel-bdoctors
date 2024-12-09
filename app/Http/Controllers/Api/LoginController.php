<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('logout');
    }

    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:50', 'exists:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ], [
                'email.required' => 'Email is required',
                'email.email' => 'Please enter a valid email address',
                'email.exists' => 'This email is not registered',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $validator->validated();

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = User::with(['specializations', 'profile'])
                       ->where('email', $credentials['email'])
                       ->firstOrFail();

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

            Log::info('User logged in successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ])->withCookie(cookie()->make(...array_values($cookieOptions)));

        } catch (\Exception $e) {
            Log::error('Login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Handle a logout request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $userId = $request->user()->id;

            // Revoke all tokens
            $request->user()->tokens()->delete();

            Log::info('User logged out successfully', ['user_id' => $userId]);

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ])->withCookie(cookie()->forget('token'));

        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'user_id' => $request->user()->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred'
            ], 500);
        }
    }
}
