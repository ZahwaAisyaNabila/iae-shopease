<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/users/{id}/orders', [UserController::class, 'getUserOrders']);

// Get all users
Route::get('/users', [UserController::class, 'index']);

// Get user by ID (dipakai oleh OrderService)
Route::get('/users/{id}', [UserController::class, 'show']);

// Create user
Route::post('/users', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
