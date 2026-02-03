<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\AuthController;
use Modules\Core\Http\Controllers\Api\VerificationController;
use Modules\Core\Http\Controllers\Api\ResetPasswordController;
use Modules\Core\Http\Controllers\Api\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Public Identity Routes
|--------------------------------------------------------------------------
*/

// Account Creation
Route::post('register', [AuthController::class, 'register']);

// Secure Login with Throttle Protection
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,5');

/*
|--------------------------------------------------------------------------
| Protected Security & Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'set_locale_lang'])->group(function () {

    // Session Termination
    Route::post('logout', [AuthController::class, 'logout']);

    // Trigger Email Verification Process
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
        ->name('verification.send');

    /** * Regional Management:
     * Handles administrative boundaries and geographical locations.
     */
    require __DIR__ . '/v1/region.php';

    /** * Access Control (RBAC):
     * Handles permissions, roles, and user-to-role assignments.
     */
    require __DIR__ . '/v1/role.php';

});

/*
|--------------------------------------------------------------------------
| Verification & Recovery (Out-of-Band)
|--------------------------------------------------------------------------
*/

// Cryptographically Signed Email Verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Password Recovery Initiation (Sends Link)
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

// Password Reset Execution
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.reset');
