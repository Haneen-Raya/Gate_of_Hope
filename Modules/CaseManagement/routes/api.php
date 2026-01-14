<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\CaseManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('casemanagements', CaseManagementController::class)->names('casemanagement');
});
