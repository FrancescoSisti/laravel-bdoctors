<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Api\ShowController;
use App\Http\Controllers\Api\CreateController;
use App\Http\Controllers\Api\EditController;
use App\Http\Controllers\Api\RegisterController as ApiRegisterController;
use App\Http\Controllers\Api\UpdateController;
use App\Http\Controllers\Auth\RegisterController as AuthRegisterController;
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
Route::post('/register', [ApiRegisterController::class, 'register'])->name('api.register');

// Specializations route
Route::get('/specializations', function () {
    $specializations = Specialization::select('id', 'name')->get();
    return response()->json([
        'specializations' => $specializations
    ]);
})->name('api.specializations');

// Profile routes
Route::get('/profiles/{id}', [ShowController::class, 'show'])->name('api.profiles.show');
Route::post('/profiles', [CreateController::class, 'create'])->name('api.profiles.create');
Route::get('/profiles/edit/{id}', [EditController::class, 'edit'])->name('api.profiles.edit');
Route::post('/profiles/edit/{id}', [UpdateController::class, 'update'])->name('api.profiles.update');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('specializations');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});
