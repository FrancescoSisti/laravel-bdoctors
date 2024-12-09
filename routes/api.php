<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ShowController;
use App\Http\Controllers\Api\CreateController;
use App\Http\Controllers\Api\EditController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UpdateController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\SpecializationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no auth required)
Route::get('/specializations', [SpecializationController::class, 'index'])->name('api.specializations');
Route::post('/login', [LoginController::class, 'login'])->name('api.login');
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User profile
    Route::get('/user', function (Request $request) {
        try {
            $user = $request->user()->load(['specializations', 'profile']);
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    })->name('api.user');

    Route::post('/logout', [LoginController::class, 'logout'])->name('api.logout');

    // Profile routes
    Route::get('/profiles/{id}', [ShowController::class, 'show'])->name('api.profiles.show');
    Route::get('/profiles/create/data', [CreateController::class, 'getData'])->name('api.profiles.create.data');
    Route::post('/profiles', [CreateController::class, 'create'])->name('api.profiles.create');
    Route::get('/profiles/edit/{id}', [EditController::class, 'edit'])->name('api.profiles.edit');
    Route::put('/profiles/{id}', [UpdateController::class, 'update'])->name('api.profiles.update');

    // Upload routes
    Route::post('/upload/file', [UploadController::class, 'store'])->name('api.upload.file');
});
