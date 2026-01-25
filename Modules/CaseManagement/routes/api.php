<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\CaseManagementController;
use Modules\CaseManagement\Http\Controllers\ServiceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('casemanagements', CaseManagementController::class)->names('casemanagement');
    Route::apiResource('services', ServiceController::class)->names('services');
});
