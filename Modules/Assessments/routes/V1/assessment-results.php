<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\Api\V1\AssessmentResultController;

/*
|--------------------------------------------------------------------------
| Assessment Results Management - API V1
|--------------------------------------------------------------------------
| Controller: AssessmentResultController
| Model: AssessmentResult
| Base Path: /api/v1/assessment-results
|--------------------------------------------------------------------------
*/

Route::prefix('assessment-results')->group(function () {

    /**
     * @name 1. List & Filter Assessment Results
     * @path GET /api/v1/assessment-results
     * * @query_params:
     * - @param beneficiary_id (int): Filter results for a specific beneficiary.
     * - @param issue_type_id (int): Filter by category (e.g., Health, Protection).
     * - @param priority (enum): Filter by (high, medium, low) - Final Priority.
     * - @param min_score (float): Lower bound for normalized score (0-100).
     * - @param max_score (float): Upper bound for normalized score (0-100).
     * - @param from_date (date): Filter by assessment date - start range.
     * - @param to_date (date): Filter by assessment date - end range.
     * - @param latest_only (bool): If 1, returns only current active assessments (Default: 1).
     * - @param page (int): Pagination page number.
     * * @features: Tagged Caching (assessment_results_global), Custom Builder, MD5 Signature.
     */
    Route::get('/', [AssessmentResultController::class, 'index'])
        ->name('assessment-results.index');

    /**
     * @name 2. Get Assessment Details
     * @path GET /api/v1/assessment-results/{id}
     * * @url_params:
     * - id (int): The unique identifier of the assessment result.
     * * @features: Dual-Layer Cache (Global + Resource Tag), Includes Assessor info.
     * @return Fresh or Cached assessment instance with related relationships.
     */
    Route::get('{id}', [AssessmentResultController::class, 'show'])
        ->name('assessment-results.show');

    /**
     * @name 3. Update Priority & Justification
     * @path PUT /api/v1/assessment-results/{assessment_result}
     * * @body_payload (UpdateAssessmentPriorityRequest):
     * - priority_final (enum/required): The specialist-confirmed priority level.
     * - justification (string/nullable): Professional reasoning for the selected priority.
     * * @description Handles professional overrides. Triggers 'AutoFlushCache' to 
     * sync dashboards and list views immediately (Ripple Effect).
     */
    Route::put('{assessment_result}', [AssessmentResultController::class, 'update'])
        ->name('assessment-results.update');

    /**
     * @name 4. Terminate Assessment Record
     * @path DELETE /api/v1/assessment-results/{assessment_result}
     * * @description Removes the assessment record. If the record was 'is_latest', 
     * the system should ideally re-evaluate the previous record's status.
     * @features: Immediate Cache Purge, Activity Log Archiving.
     */
    Route::delete('{assessment_result}', [AssessmentResultController::class, 'destroy'])
        ->name('assessment-results.destroy');
});