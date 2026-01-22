<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\AssessmentsController;
use Modules\Assessments\Http\Controllers\V1\GoogleFormController;
use Modules\Assessments\Http\Controllers\V1\PriorityRuleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('assessments', AssessmentsController::class)->names('assessments');
});
    Route::prefix('v1')->group(function () {

    Route::apiResource('priority-rules', PriorityRuleController::class);

    Route::prefix('google-forms')->group(function () {
        Route::get('/', [GoogleFormController::class, 'index']);
        Route::post('/', [GoogleFormController::class, 'store']);
        Route::get('{id}', [GoogleFormController::class, 'show']);
        Route::put('{id}', [GoogleFormController::class, 'update']);
        Route::delete('{id}', [GoogleFormController::class, 'destroy']);
        Route::get('/issue-type/{issue_type_id}', [GoogleFormController::class, 'getByIssueType']);
        Route::post('/import', [GoogleFormController::class, 'importResults']);


    });
});
