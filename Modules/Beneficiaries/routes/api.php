<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\EducationLevelController;
use Modules\Beneficiaries\Http\Controllers\EmploymentStatusController;
use Modules\Beneficiaries\Http\Controllers\HousingTypeController;
use Modules\Beneficiaries\Http\Controllers\SocialBackgroundController;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Register beneficiaries routes
    require __DIR__ . '/V1/beneficiaries.php';

    Route::apiResource('education_levels', EducationLevelController::class)->names('educationlevels');
    Route::patch('education_levels/{education_level}/updateActivation', [EducationLevelController::class, 'updateActivation']);
    Route::apiResource('housing_types', HousingTypeController::class)->names('housingtypes');
    Route::patch('housing_types/{housing_type}/updateActivation', [HousingTypeController::class, 'updateActivation']);
    Route::apiResource('employment_statuses', EmploymentStatusController::class)->names('employmentstatuses');
    Route::patch('employment_statuses/{employment_status}/updateActivation', [EmploymentStatusController::class, 'updateActivation']);
    Route::apiResource('social_backgrounds', SocialBackgroundController::class)->names('socailbackgrounds');
});
