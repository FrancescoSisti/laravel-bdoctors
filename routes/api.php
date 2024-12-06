<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ShowController;
use App\Http\Controllers\Api\CreateController;
use App\Http\Controllers\Api\EditController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UpdateController;
use App\Models\Specialization;

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

// Public routes
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('/auth/login', [LoginController::class, 'login'])->name('api.login');
    Route::post('/auth/register', [RegisterController::class, 'register'])->name('api.register');

    // Public data routes
    Route::get('/specializations', function () {
        return response()->json([
            'success' => true,
            'data' => Specialization::select('id', 'name')->get()
        ]);
    })->name('api.specializations');

    Route::get('/profiles/{id}', [ShowController::class, 'show'])->name('api.profiles.show');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User routes
        Route::get('/user', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => $request->user()->load('specializations')
            ]);
        });
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');

        // Profile management routes
        Route::prefix('profiles')->group(function () {
            Route::post('/', [CreateController::class, 'create'])->name('api.profiles.create');
            Route::get('/{id}/edit', [EditController::class, 'edit'])->name('api.profiles.edit');
            Route::put('/{id}', [UpdateController::class, 'update'])->name('api.profiles.update');
        });
    });
});