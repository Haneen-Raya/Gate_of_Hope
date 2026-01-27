<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\CaseReviewController;

/*
|--------------------------------------------------------------------------
| Case Review Management - API V1
|--------------------------------------------------------------------------
| Controller: CaseReviewController
| Model: CaseReview
| Base Path: /api/v1/case-reviews
|--------------------------------------------------------------------------
*/

Route::prefix('case-reviews')->group(function () {

    /**
     * @name 1. List & Filter Case Reviews
     * @path GET /api/v1/case-reviews
     * * @query_params:
     * - @param case_id (int): Filter by a specific beneficiary case.
     * - @param specialist_id (int): Filter by the conducting specialist.
     * - @param progress_status (string): trajectory filter (improving, stable, worsening).
     * - @param from_date/to_date (date): Temporal range filtering for session dates.
     * - @param latest_only (bool): Flag to retrieve the most recent assessment.
     * * @features: Tagged Caching (Global/Resource), MD5 Signature Generation, ksort Normalization.
     */
    Route::get('/', [CaseReviewController::class, 'index'])
        ->name('case-reviews.index');

    /**
     * @name 2. Store New Case Review
     * @path POST /api/v1/case-reviews
     * * @body_payload (StoreCaseReviewRequest):
     * - beneficiary_case_id (int/required): Reference to the parent case.
     * - progress_status (string/required): Clinical assessment state.
     * - reviewed_at (timestamp/required): The actual date of evaluation (<= now).
     * - notes (text/nullable): Detailed specialist observations.
     * * @description Automates specialist_id attribution via Auth context.
     */
    Route::post('/', [CaseReviewController::class, 'store'])
        ->name('case-reviews.store');

    /**
     * @name 3. Get Specific Review Details
     * @path GET /api/v1/case-reviews/{id}
     * * @url_params:
     * - id (int): Primary identifier of the review record.
     * * @features: Dual-Layer Caching (Global Tag + Resource Specific Tag).
     */
    Route::get('{id}', [CaseReviewController::class, 'show'])
        ->name('case-reviews.show');

    /**
     * @name 4. Update Review Insights
     * @path PUT/PATCH /api/v1/case-reviews/{case_review}
     * * @description Manages refinement of assessment data.
     * - Note: beneficiary_case_id is 'prohibited' to ensure reference immutability.
     * - Triggers: AutoFlushCache on 'case_reviews' and 'case_review_{id}'.
     */
    Route::put('{case_review}', [CaseReviewController::class, 'update'])
        ->name('case-reviews.update');

    /**
     * @name 5. Delete Case Review Record
     * @path DELETE /api/v1/case-reviews/{case_review}
     * * @description Performs hard deletion and executes a 'Ripple Effect' cache purge 
     * to maintain real-time data consistency for M&E dashboards.
     */
    Route::delete('{case_review}', [CaseReviewController::class, 'destroy'])
        ->name('case-reviews.destroy');
});