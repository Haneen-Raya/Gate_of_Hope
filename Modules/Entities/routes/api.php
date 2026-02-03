<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\Api\V1\EntitiyController;

Route::middleware(['auth:sanctum','set_locale_lang'])->prefix('v1')->group(function () {

    require __DIR__ . '/V1/donor-reports.php';
    //Route::apiResource('entities', EntitiyController::class)->names('entities');

    // Register Program Fundings routes
    require __DIR__ . '/V1/program_fundings.php';

    // Register entities routes
    require __DIR__ . '/V1/entities.php';
});
