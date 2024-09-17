<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
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
Route::middleware('auth:sanctum')->get('/trips', [TripController::class, 'index']);
// Get a specific trip by ID
Route::middleware('auth:sanctum')->get('/trips/{trip}', [TripController::class, 'show']);

Route::prefix('users')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/trips/{trip}/seats', [BookingController::class, 'getSeats']);
        Route::post('/trips/{trip}/seats/book', [BookingController::class, 'bookSeats']);
        Route::put('/trips/{trip}/seats/{booking}/update', [BookingController::class, 'updateSeat']);
        Route::get('/user/bookings/pending', [BookingController::class, 'getPendingBookings']);

        Route::get('profile', [UserController::class, 'profile']);
        Route::post('logout', [UserController::class, 'logout']);
        Route::put('update-password', [UserController::class, 'updatePassword']);
    });
});

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Create a new trip
    Route::post('/trips', [TripController::class, 'store']);


    // Update a specific trip by ID
    Route::put('/trips/{trip}', [TripController::class, 'update']);

    // Delete a specific trip by ID
    Route::delete('/trips/{trip}', [TripController::class, 'destroy']);

    Route::post('/bookings/approve', [AdminController::class, 'approveBookings']);
    Route::post('/bookings/reject', [AdminController::class, 'rejectBookings']);
    Route::post('/user/register', [AdminController::class, 'registerWithoutPassword']);
    Route::get('/non-admin', [AdminController::class, 'getNonAdminUsers']);
    Route::get('/trips/{trip}/seats', [AdminController::class, 'getSeats']);
    Route::get('/dashboard-stats', [AdminController::class, 'getInfo']);
    Route::get('users', [AdminController::class, 'index']);
    Route::get('users/{user}', [AdminController::class, 'show']);
    Route::put('users/{user}', [AdminController::class, 'update']);
    Route::delete('users/{user}', [AdminController::class, 'destroy']);
    Route::put('users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin']);
    Route::apiResource('templates', TemplateController::class);
});
