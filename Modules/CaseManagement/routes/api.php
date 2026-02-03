<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\CaseReferralController;
use Modules\CaseManagement\Http\Controllers\Api\V1\ServiceController;
use Modules\CaseManagement\Http\Controllers\CaseEventController;
use Modules\CaseManagement\Http\Controllers\CaseManagementController;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','set_locale_lang'])->prefix('v1')->group(function () {

    // Register case managements routes
    require __DIR__ . '/v1/case_sessions.php';
    Route::apiResource('casemanagements', CaseManagementController::class)->names('casemanagement');

    // Register Case Event routes
    require __DIR__ . '/V1/case-events.php';

    // Register Case Support Plans routes
    require __DIR__ . '/V1/case-support-plans.php';

    // Register Case Plan Goals routes
    require __DIR__ . '/V1/case-plan-goals.php';

    // Register Case Reviews routes
    require __DIR__ . '/V1/case-reviews.php';

    // Register Case Referrals routes
    require __DIR__ . '/V1/case-referrals.php';

    // Register Services routes
    require __DIR__ . '/V1/services.php';

    require __DIR__ . '/V1/beneficiary-case.php';
});
