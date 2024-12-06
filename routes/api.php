<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::post('/login', [LoginController::class, 'login'])->name('api.login');
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
Route::get('/specializations', function () {
    return response()->json([
        'specializations' => Specialization::select('id', 'name')->orderBy('name')->get()
    ]);
})->name('api.specializations');

// Protected routes
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load(['specializations', 'profile'])
        ]);
    })->name('api.user');

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Profile routes
    Route::get('/profiles/{id}', [ShowController::class, 'show'])->name('api.profiles.show');
    Route::post('/profiles', [CreateController::class, 'create'])->name('api.profiles.create');
    Route::get('/profiles/edit/{id}', [EditController::class, 'edit'])->name('api.profiles.edit');
    Route::put('/profiles/{id}', [UpdateController::class, 'update'])->name('api.profiles.update');
});