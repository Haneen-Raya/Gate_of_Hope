<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','set_locale_lang'])->prefix('v1')->group(function () {

    // Register beneficiaries routes
    require __DIR__ . '/V1/beneficiaries.php';

    // Register education levels routes
    require __DIR__ . '/V1/education_levels.php';

    // Register employment Statuses routes
    require __DIR__ . '/V1/employment_statuses.php';

    // Register housing types routes
    require __DIR__ . '/V1/housing_types.php';

    // Register social backgrounds routes
    require __DIR__ . '/V1/social_backgrounds.php';

});
