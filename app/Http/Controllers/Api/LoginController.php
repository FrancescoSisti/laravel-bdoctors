<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:50', 'exists:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            if (!Auth::attempt($validated)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            // Load necessary relationships
            $user->load(['specializations', 'profile']);

            // Create token
            $token = $user->createToken('auth-token')->plainTextToken;

            $response = response()->json([
                'success' => true,
                'data' => $user,
                'token' => $token
            ]);

            // Set cookie with token
            return $response->withCookie(
                cookie(
                    'token',
                    $token,
                    60 * 24, // 24 hours
                    '/',
                    null,
                    config('app.env') === 'production', // secure only in production
                    true, // httpOnly
                    false,
                    'lax' // sameSite
                )
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke the token that was used to authenticate the current request
            $request->user()->currentAccessToken()->delete();

            // Clear session
            Session::flush();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ])->withCookie(cookie()->forget('token'));

        } catch (\Exception $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}