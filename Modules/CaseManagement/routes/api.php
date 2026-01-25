<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\ServiceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('services', ServiceController::class)->names('services');

    // Register Case Support Plans routes
    require __DIR__ . '/V1/case-support-plans.php';
});
