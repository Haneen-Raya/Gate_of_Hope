<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\Api\V1\EntitiyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('entities', EntitiyController::class)->names('entities');
});
