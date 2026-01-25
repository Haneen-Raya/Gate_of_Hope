<?php

use Illuminate\Support\Facades\Route;
use Modules\HumanResources\Http\Controllers\Api\V1\HumanResourcesController;
use Modules\HumanResources\Http\Controllers\Api\V1\SpecialistController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('humanresources', HumanResourcesController::class)->names('humanresources');
        require __DIR__ . '/v1/specialists.php';
});
