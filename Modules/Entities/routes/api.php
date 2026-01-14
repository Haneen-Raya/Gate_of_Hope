<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\EntitiesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('entities', EntitiesController::class)->names('entities');
});
