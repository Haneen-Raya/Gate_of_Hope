<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\CaseReferralController;
use Modules\CaseManagement\Http\Controllers\Api\V1\ServiceController;
use Modules\CaseManagement\Http\Controllers\CaseManagementController;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Register case managements routes
    require __DIR__ . '/v1/case_sessions.php';
    Route::apiResource('casemanagements', CaseManagementController::class)->names('casemanagement');

    Route::apiResource('services', ServiceController::class)->names('services');
    //Route::apiResource('case-referrals', CaseReferralController::class);

    // Register Case Support Plans routes
    require __DIR__ . '/V1/case-support-plans.php';

    // Register Case Plan Goals routes
    require __DIR__ . '/V1/case-plan-goals.php';

    // Register Case Reviews routes
    require __DIR__ . '/V1/case-reviews.php';
    
    // Register Case Referrals routes
    require __DIR__ . '/V1/case-referrals.php';
});
require __DIR__ . '/V1/beneficiary-case.php';
