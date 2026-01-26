<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\CaseManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Register case managements routes
    require __DIR__ . '/v1/case_sessions.php';
    Route::apiResource('casemanagements', CaseManagementController::class)->names('casemanagement');
});