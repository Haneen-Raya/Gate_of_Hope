<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\Api\V1\GoogleFormController;

/*
|--------------------------------------------------------------------------
| Google Forms Integration - API V1 Routes
|--------------------------------------------------------------------------
| Purpose: Maps external assessment tools to internal system categories.
| Controller: GoogleFormController
| Features: Issue Type mapping, Bulk CSV/XLSX imports, URL validation.
|--------------------------------------------------------------------------
*/

Route::prefix('v1/google-forms')->group(function () {

    /**
     * @name 1. List Form Mappings
     * @path GET /api/v1/google-forms
     * @features: Paginated list with 'issueType' eager loading.
     */
    Route::get('/', [GoogleFormController::class, 'index']);

    /**
     * @name 2. Create Form Link
     * @path POST /api/v1/google-forms
     * @features: Ensures one-to-one mapping between IssueType and Google Form.
     */
    Route::post('/', [GoogleFormController::class, 'store']);

    /**
     * @name 3. Get Form Details
     * @path GET /api/v1/google-forms/{id}
     */
    Route::get('/{id}', [GoogleFormController::class, 'show']);

    /**
     * @name 4. Update Form Mapping
     * @path PUT /api/v1/google-forms/{id}
     * @features: Auto-syncs URLs in cached responses upon update.
     */
    Route::put('/{id}', [GoogleFormController::class, 'update']);

    /**
     * @name 5. Delete Form Link
     * @path DELETE /api/v1/google-forms/{id}
     */
    Route::delete('/{id}', [GoogleFormController::class, 'destroy']);

    /**
     * @name 6. Find Form by Issue Type
     * @path GET /api/v1/google-forms/issue-type/{issue_type_id}
     * @description: High-speed lookup for frontend form redirection.
     */
    Route::get('/issue-type/{issue_type_id}', [GoogleFormController::class, 'getByIssueType']);

    /**
     * @name 7. Import Assessment Results
     * @path POST /api/v1/google-forms/import
     * @description: Endpoint to upload and process Excel/CSV results into database records.
     */
    Route::post('/import', [GoogleFormController::class, 'importResults']);
});
