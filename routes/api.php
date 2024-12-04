<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ShowController;
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
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');

// Specializations route
Route::get('/specializations', function () {
    $specializations = Specialization::select('id', 'name')->get();
    return response()->json([
        'specializations' => $specializations
    ]);
})->name('api.specializations');

// Profile routes
Route::get('/profiles/{id}', [ShowController::class, 'show'])->name('api.profiles.show');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('specialization');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});
