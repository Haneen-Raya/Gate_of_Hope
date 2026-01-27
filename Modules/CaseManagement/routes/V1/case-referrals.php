<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\CaseReferralController;

/*
|--------------------------------------------------------------------------
| Case Referral Management - API V1
|--------------------------------------------------------------------------
| Controller: CaseReferralController
| Model: CaseSupportPlan
| Base Path: /api/v1/case-referrals
|--------------------------------------------------------------------------
*/

Route::prefix('case-referrals')->group(function () {

    /**
     * @name 1. List & Search Support Plans
     * @path GET /api/v1/case-support-plans
     *
     * @query_params:
     * - @param beneficiary_case_id (int): Filter by a specific case ID.
     * - @param version (int): Filter by plan version number.
     * - @param is_active (bool): Filter by current active status (0/1).
     * - @param start_date (date): Filter plans starting on/after YYYY-MM-DD.
     * - @param end_date (date): Filter plans ending before/on YYYY-MM-DD.
     * - @param page (int): Pagination page number (default: 1).
     *
     * @features: Deterministic Tagged Caching, MD5 Signature Key, Custom Query Builder.
     */
    Route::get('/', [CaseReferralController::class, 'index']);

    /**
     * @name 2. Store New Support Plan
     * @path POST /api/v1/case-support-plans
     *
     * @body_payload (StoreCaseSupportPlanRequest):
     * - beneficiary_case_id (int/required): The case this plan belongs to.
     * - version (int/required): Plan iteration identifier.
     * - is_active (bool/optional): Defaults to false unless specified.
     * - start_date (date/required): Commencement of the plan (>= today).
     * - end_date (date/required): Completion of the plan (> start_date).
     *
     * @description Persists a new plan, assigns current auth user to audit fields,
     * and invalidates global list cache.
     */
    Route::post('/', [CaseReferralController::class, 'store']);

    /**
     * @name 3. Get Support Plan Profile
     * @path GET /api/v1/case-support-plans/{id}
     *
     * @url_params:
     * - id (int): The unique ID of the support plan.
     *
     * @return Full JSON object with goals and audit metadata.
     *
     * @note: Uses ID-based retrieval to maximize Service Layer Cache Hits.
     */
    Route::get('{case-referral}', [CaseReferralController::class, 'show']);

    /**
     * @name 4. Full/Partial Update
     * @path PUT /api/v1/case-support-plans/{case_support_plan}
     *
     * @description Updates plan attributes and purges dual-tag cache:
     * 1. Individual record tag (case_support_plan_{id})
     * 2. Global list tag (case_support_plans)
     */
    Route::put('{case-referral}', [CaseReferralController::class, 'update']);

    /**
     * @name 5. Delete Support Plan
     * @path DELETE /api/v1/case-support-plans/{case_support_plan}
     *
     * @description Permanently or Soft deletes the record. Triggers cache flush
     * for the specific resource and all paginated lists.
     */
    Route::delete('{case-referral}', [CaseReferralController::class, 'destroy']);
});
