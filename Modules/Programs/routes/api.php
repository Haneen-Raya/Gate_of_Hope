<?php

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\ProgramsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
                require __DIR__ . '/v1/activity-sessions.php';
    Route::apiResource('programs', ProgramsController::class)->names('programs');

    // Register activities routes
    require __DIR__ . '/V1/activities.php';

    // Register activity attendances routes
    require __DIR__ . '/V1/activity_attendances.php';
});
