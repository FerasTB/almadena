<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [UserController::class, 'profile']);
        Route::post('logout', [UserController::class, 'logout']);
        Route::put('update-password', [UserController::class, 'updatePassword']);
    });
});

Route::prefix('admin')->middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('users', [AdminController::class, 'index']);
    Route::get('users/{user}', [AdminController::class, 'show']);
    Route::put('users/{user}', [AdminController::class, 'update']);
    Route::delete('users/{user}', [AdminController::class, 'destroy']);
    Route::put('users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin']);
    Route::apiResource('trips', TripController::class);
    Route::apiResource('templates', TemplateController::class);
});
