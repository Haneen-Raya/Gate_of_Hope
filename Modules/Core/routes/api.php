<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\AuthController;
use Modules\Core\Http\Controllers\Api\VerificationController;
use Modules\Core\Http\Controllers\Api\ResetPasswordController;
use Modules\Core\Http\Controllers\Api\ForgotPasswordController;
use Modules\Core\Http\Controllers\Api\RoleManagementController;

Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,5');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
        ->middleware(['auth:sanctum'])
        ->name('verification.send');
});

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/roles', [RoleManagementController::class, 'index']);
    Route::post('/users/{user}/roles/assign', [RoleManagementController::class, 'assign']);
    Route::post('/users/{user}/roles/revoke', [RoleManagementController::class, 'revoke']);
    Route::put('/users/{user}/roles/update', [RoleManagementController::class, 'update']);
    });
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');


Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.reset');
