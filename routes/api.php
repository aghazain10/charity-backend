<?php

// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ProfileController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::put('/profile', [ProfileController::class, 'update']);
    // Campaign routes
    Route::apiResource('campaigns', CampaignController::class);
    Route::get('/my-campaigns', [CampaignController::class, 'myCampaigns']);

    // Donation routes
    Route::apiResource('donations', DonationController::class);

    Route::get('/my-donations', [DonationController::class, 'myDonations']);

    Route::get('/profile', [AuthController::class, 'profile']);
});

// Public routes
Route::get('/public/campaigns', [CampaignController::class, 'indexPublic']);
Route::get('/public/campaigns/{campaign}', [CampaignController::class, 'showPublic']);

Route::get('/campaigns/{campaign}/donations', [DonationController::class, 'campaignDonations']);



Route::prefix('password')->group(function () {
    // Change password (requires auth - logged in user)
    Route::middleware('auth:sanctum')->post('/change', [PasswordController::class, 'changePassword']);
    Route::middleware('auth:sanctum')->post('/confirm-change/{token}', [PasswordController::class, 'confirmPasswordChange']);

    // Forgot password (no auth required - user forgot password)
    Route::post('/forgot', [PasswordController::class, 'forgotPassword']);
    Route::post('/reset', [PasswordController::class, 'resetPassword']);
});
Route::post('/profile/confirm-password-change/{token}', [PasswordController::class, 'confirmPasswordChange']);
