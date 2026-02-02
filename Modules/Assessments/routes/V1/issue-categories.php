<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\Api\V1\IssueCategoryController;

/*
|--------------------------------------------------------------------------
| Issue Categories Management - API V1 Routes
|--------------------------------------------------------------------------
| Purpose:
| - Manage high-level issue classifications used in assessments.
| - Acts as the parent entity for Issue Types.
|
| Features:
| - Soft Deletes & Restore
| - Active / Inactive filtering
| - Used in scoring & priority logic
|--------------------------------------------------------------------------
*/

Route::prefix('assessment/issue-categories')->group(function () {

    /**
     * ------------------------------------------------------------------
     * 1. List All Issue Categories
     * ------------------------------------------------------------------
     * @path    GET /api/v1/assessment/issue-categories
     * @features:
     * - Pagination support
     * - Optional filters
     */
    Route::get('/', [IssueCategoryController::class, 'index']);

    /**
     * ------------------------------------------------------------------
     * 2. List Active Issue Categories
     * ------------------------------------------------------------------
     * @path    GET /api/v1/assessment/issue-categories/active
     * @features:
     * - Returns only active categories
     */
    Route::get('/active', [IssueCategoryController::class, 'active']);

    /**
     * ------------------------------------------------------------------
     * 3. Store New Issue Category
     * ------------------------------------------------------------------
     * @path    POST /api/v1/assessment/issue-categories
     * @features:
     * - Validation handled by FormRequest
     * - Cache invalidation (if enabled)
     */
    Route::post('/', [IssueCategoryController::class, 'store']);

    /**
     * ------------------------------------------------------------------
     * 4. Show Issue Category Details
     * ------------------------------------------------------------------
     * @path    GET /api/v1/assessment/issue-categories/{issueCategory}
     * @features:
     * - Route Model Binding
     */
    Route::get('{issueCategory}', [IssueCategoryController::class, 'show']);

    /**
     * ------------------------------------------------------------------
     * 5. Update Issue Category
     * ------------------------------------------------------------------
     * @path    PUT /api/v1/assessment/issue-categories/{issueCategory}
     * @features:
     * - Partial updates supported
     */
    Route::put('{issueCategory}', [IssueCategoryController::class, 'update']);

    /**
     * ------------------------------------------------------------------
     * 6. Delete Issue Category (Soft Delete)
     * ------------------------------------------------------------------
     * @path    DELETE /api/v1/assessment/issue-categories/{issueCategory}
     */
    Route::delete('{issueCategory}', [IssueCategoryController::class, 'destroy']);

    /**
     * ------------------------------------------------------------------
     * 7. Restore Deleted Issue Category
     * ------------------------------------------------------------------
     * @path    POST /api/v1/assessment/issue-categories/{id}/restore
     */
    Route::post('{id}/restore', [IssueCategoryController::class, 'restore']);
});
