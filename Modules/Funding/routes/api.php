<?php

use Illuminate\Support\Facades\Route;
use Modules\Funding\Http\Controllers\FundingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('fundings', FundingController::class)->names('funding');
});
