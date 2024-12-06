<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EditProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\CreateController;
use App\Http\Controllers\EditController;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// All routes are public now
Route::get('/specializations', function () {
    return response()->json([
        'specializations' => Specialization::select('id', 'name')->orderBy('name')->get()
    ]);
})->name('api.specializations');

Route::get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user()->load(['specializations', 'profile'])
    ]);
})->name('api.user');

// Auth routes
Route::post('/login', [LoginController::class, 'login'])->name('api.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');

// Profile routes
Route::get('/profiles/{id}', [ShowController::class, 'show'])->name('api.profiles.show');
Route::post('/profiles', [CreateController::class, 'create'])->name('api.profiles.create');
Route::get('/profiles/edit/{id}', [EditController::class, 'edit'])->name('api.profiles.edit');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('specialization');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});