<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\AssessmentsController;

/**
 * Assessments & Priority Module - Version 1
 * * Global Protection: Authenticated access with dynamic localization
 * for multilingual assessment forms and reports.
 */

Route::middleware(['auth:sanctum', 'set_locale_lang'])->prefix('v1')->group(function () {

    /** * Master Assessment Resource:
     * Standard CRUD for creating and managing assessment templates.
     */
    Route::apiResource('assessments', AssessmentsController::class)->names('assessments');

    /** * Scoring & Priority Logic:
     * Routes for defining the business rules that calculate priority scores.
     */
    require __DIR__ . '/V1/priority-rules.php';

    /** * Data Collection Integration:
     * Handles endpoints for Google Forms integration and data mapping.
     */
    require __DIR__ . '/V1/google-forms.php';

    /** * Issue Hierarchies:
     * Classification routes for grouping and typing beneficiary issues.
     */
    require __DIR__ . '/V1/issue-categories.php';
    require __DIR__ . '/V1/issue-types.php';

    /** * Evaluation Outcomes:
     * Manages the storage and retrieval of completed assessment results.
     */
    require __DIR__ . '/V1/assessment-results.php';
});
