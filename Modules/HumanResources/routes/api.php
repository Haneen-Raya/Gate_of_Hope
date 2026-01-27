<?php

use Illuminate\Support\Facades\Route;
use Modules\HumanResources\Http\Controllers\Api\V1\ProfessionController;
use Modules\HumanResources\Http\Controllers\HumanResourcesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('humanresources', HumanResourcesController::class)->names('humanresources');

    // Register professions routes
    require __DIR__ . '/V1/professions.php';
});
