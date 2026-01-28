<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\Api\V1\EducationLevelController;
use Modules\Beneficiaries\Http\Controllers\Api\V1\EmploymentStatusController;
use Modules\Beneficiaries\Http\Controllers\Api\V1\HousingTypeController;
use Modules\Beneficiaries\Http\Controllers\Api\V1\SocialBackgroundController;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

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
