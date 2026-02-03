<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\Api\V1\IssueTypeController;

/*
|--------------------------------------------------------------------------
| Issue Types Management - API V1 Routes
|--------------------------------------------------------------------------
| Purpose:
| - Manage specific issue types under an Issue Category.
| - Used directly in assessments & scoring logic.
|
| Relationships:
| - Belongs to Issue Category
|
| Features:
| - Soft Deletes & Restore
| - Activate / Deactivate
|--------------------------------------------------------------------------
*/

Route::prefix('v1/assessment/issue-types')->group(function () {

    /**
     * ------------------------------------------------------------------
     * 1. List All Issue Types
     * ------------------------------------------------------------------
     * @path    GET /api/v1/assessment/issue-types
     * @features:
     * - Filter by issue_category_id
     */
    Route::get('/', [IssueTypeController::class, 'index']);

    /**
     * ------------------------------------------------------------------
     * 2. List Active Issue Types
     * ------------------------------------------------------------------
     * @path    GET /api/v1/assessment/issue-types/active
     */
    Route::get('/active', [IssueTypeController::class, 'active']);

    /**
     * ------------------------------------------------------------------
     * 3. Store New Issue Type
     * ------------------------------------------------------------------
     * @path    POST /api/v1/assessment/issue-types
     * @features:
     * - Must be linked to Issue Category
     */
    Route::post('/', [IssueTypeController::class, 'store']);

    /**
     * ------------------------------------------------------------------
     * 4. Show Issue Type Details
     * ------------------------------------------------------------------
     * @path    GET /api/v1/assessment/issue-types/{issueType}
     */
    Route::get('{issueType}', [IssueTypeController::class, 'show']);

    /**
     * ------------------------------------------------------------------
     * 5. Update Issue Type
     * ------------------------------------------------------------------
     * @path    PUT /api/v1/assessment/issue-types/{issueType}
     */
    Route::put('{issueType}', [IssueTypeController::class, 'update']);

    /**
     * ------------------------------------------------------------------
     * 6. Delete Issue Type (Soft Delete)
     * ------------------------------------------------------------------
     * @path    DELETE /api/v1/assessment/issue-types/{issueType}
     */
    Route::delete('{issueType}', [IssueTypeController::class, 'destroy']);

    /**
     * ------------------------------------------------------------------
     * 7. Restore Deleted Issue Type
     * ------------------------------------------------------------------
     * @path    POST /api/v1/assessment/issue-types/{id}/restore
     */
    Route::post('{id}/restore', [IssueTypeController::class, 'restore']);

    /**
     * ------------------------------------------------------------------
     * 8. Deactivate Issue Type
     * ------------------------------------------------------------------
     * @path    POST /api/v1/assessment/issue-types/{issueType}/deactivate
     * @features:
     * - Keeps record but hides from active usage
     */
    Route::post('{issueType}/deactivate', [IssueTypeController::class, 'deactivate']);
});
