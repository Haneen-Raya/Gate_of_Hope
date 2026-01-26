<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\Api\V1\IssueCategoryController;
use Modules\Assessments\Http\Controllers\Api\V1\IssueTypeController;
use Modules\Assessments\Http\Controllers\AssessmentsController;
use Modules\Assessments\Http\Controllers\Api\V1\GoogleFormController;
use Modules\Assessments\Http\Controllers\Api\V1\PriorityRuleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('assessments', AssessmentsController::class)->names('assessments');
    // ----------------- Issue Categories -----------------
    Route::prefix('assessment/issue-categories')->group(function () {

        Route::get('/', [IssueCategoryController::class, 'index']);
        Route::get('/active', [IssueCategoryController::class, 'active']);
        Route::post('/', [IssueCategoryController::class, 'store']);
        Route::get('{issueCategory}', [IssueCategoryController::class, 'show']);
        Route::put('{issueCategory}', [IssueCategoryController::class, 'update']);
        Route::delete('{issueCategory}', [IssueCategoryController::class, 'destroy']);
        Route::post('{id}/restore', [IssueCategoryController::class, 'restore']);
    });
    // ----------------- Issue Types -----------------
    Route::prefix('assessment/issue-types')->group(function () {

        Route::get('/', [IssueTypeController::class, 'index']);
        Route::get('/active', [IssueTypeController::class, 'active']);
        Route::post('/', [IssueTypeController::class, 'store']);
        Route::get('{issueType}', [IssueTypeController::class, 'show']);
        Route::put('{issueType}', [IssueTypeController::class, 'update']);
        Route::delete('{issueType}', [IssueTypeController::class, 'destroy']);
        Route::post('{id}/restore', [IssueTypeController::class, 'restore']);
        Route::post('{issueType}/deactivate', [IssueTypeController::class, 'deactivate']);
    });
});


    require __DIR__ . '/V1/priority-rules.php';
    require __DIR__ . '/V1/google-forms.php';
