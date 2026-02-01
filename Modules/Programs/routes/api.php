<?php

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\ProgramsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
                require __DIR__ . '/v1/activity-sessions.php';
    Route::apiResource('programs', ProgramsController::class)->names('programs');
});
