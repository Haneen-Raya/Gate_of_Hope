<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\CaseReferralController;

/*
|--------------------------------------------------------------------------
| Case Referral Management - API V1
|--------------------------------------------------------------------------
| Controller: CaseReferralController
| Model: CaseReferral
| Base Path: /api/v1/case-referrals
|--------------------------------------------------------------------------
*/

Route::prefix('case-referrals')->group(function () {

    /**
     * @name 1. List & Search Case Referrals
     * @path GET /api/v1/case-referrals
     *
     * @query_params:
     * - @param beneficiary_case_id (int): Filter by a specific case ID.
     * - @param service_id (int): Filter by a specific service ID.
     * - @param receiver_entity_id (int): Filter by a specific receiver entity ID.
     * - @param type (string): Filter by referral type.
     * - @param direction (string): Filter by referral direction.
     * - @param status (string): Filter by referral status.
     * - @param urgency_level (string): Filter by referral urgency_level.
     * - @param referral_date_from (date): Filter referrals starting on/after YYYY-MM-DD.
     * - @param referral_date_to (date): Filter plans ending before/on YYYY-MM-DD.
     * - @param rejected (bool):  Quick filter for rejected referrals.
     * - @param completed (bool):  Quick filter for completed referrals.
     * - @param cancelled (bool):  Quick filter for cancelled referrals.
     * - @param accepted (bool):  Quick filter for accepted referrals.
     * - @param page (int): Pagination page number (default: 1).
     *
     * @features: Deterministic Tagged Caching, MD5 Signature Key, Custom Query Builder.
     */
    Route::get('/', [CaseReferralController::class, 'index']);

    /**
     * @name 2. Store New Case Referral
     * @path POST /api/v1/case-referrals
     *
     * @body_payload (StoreCaseReferralRequest):
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
     * @name 3. Get Case Referral Profile
     * @path GET /api/v1/case-referrals/{case_referral}
     *
     * @url_params:
     * - case_referral (Case Referral): The object of the Case Referral.
     *
     * @return Full JSON object with goals and audit metadata.
     *
     * @note: Uses ID-based retrieval to maximize Service Layer Cache Hits.
     */
    Route::get('{case_referral}', [CaseReferralController::class, 'show']);

    /**
     * @name 4. Full/Partial Update
     * @path PUT /api/v1/case-referrals-plans/{case_referral}
     *
     * @description Updates referral attributes and purges dual-tag cache:
     * 1. Individual record tag (case_referral_{id})
     * 2. Global list tag (case_referrals)
     */
    Route::put('{case_referral}', [CaseReferralController::class, 'update']);

    /**
     * @name 5. Delete Case Referral
     * @path DELETE /api/v1/case-referrals-plans/{case_referral}
     *
     * @description Permanently or Soft deletes the record. Triggers cache flush
     * for the specific resource and all paginated lists.
     */
    Route::delete('{case_referral}', [CaseReferralController::class, 'destroy']);

    /**
     * @name 6. FUpdate Referral Status
     * @path PUT /api/v1/case-referrals/{case_referral}/updateStatus
     *
     * * @url_params:
     * - case_referral (Case Referral): The object of the Case Referral.
     *
     * @body_payload:
     * - status (string/required): New referral status (pending, approved, rejected).
     * - notes (string/optional): Optional note for status change.
     *
     * @description Updates only the status of the referral and invalidates relevant cache tags.
     */
    Route::put('{case_referral}/updateStatus', [CaseReferralController::class, 'updateStatus']);
});
