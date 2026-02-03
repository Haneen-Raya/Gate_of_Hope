<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\CaseReferralController;

/*
|--------------------------------------------------------------------------
| Case Referral Management Module - API V1
|--------------------------------------------------------------------------
|
| Controller : CaseReferralController
| Model      : CaseReferral
| Base Path  : /api/v1/case-referrals
|
| CaseReferrals represent referrals of beneficiary cases to specific services,
| including direction, urgency, status, and lifecycle tracking (accepted, rejected, completed, cancelled).
|
|--------------------------------------------------------------------------
*/

Route::prefix('case-referrals')->group(function () {

    /*
     * ----------------------------------------------------------------------
     * 1. List & Search Case Referrals
     * ----------------------------------------------------------------------
     *
     * @name   CaseReferral Index
     * @route  GET /api/v1/case-referrals
     *
     * @description
     * Returns a paginated list of case referrals with dynamic filtering support.
     *
     * @queryParams
     * - beneficiary_case_id   (int|null)    Filter by beneficiary case
     * - service_id            (int|null)    Filter by requested service
     * - receiver_entity_id    (int|null)    Filter by receiving entity
     * - referral_type         (string|null) Filter by referral type
     * - direction             (string|null) Filter by referral direction
     * - status                (string|null) Filter by referral status
     * - urgency_level         (string|null) Filter by urgency level
     * - referral_date_from    (date|null)   Filter referrals from this date
     * - referral_date_to      (date|null)   Filter referrals to this date
     * - rejected              (bool|null)   Only rejected referrals
     * - accepted              (bool|null)   Only accepted referrals
     * - completed             (bool|null)   Only completed referrals
     * - cancelled             (bool|null)   Only cancelled referrals
     * - page                  (int)         Pagination page number (default: 1)
     *
     * @features
     * - Custom CaseReferralBuilder Filters
     * - Tagged Caching
     * - Pagination Support
     */
    Route::get('/', [CaseReferralController::class, 'index'])
        ->name('case-referrals.index');

    /*
     * ----------------------------------------------------------------------
     * 2. Store New Case Referral
     * ----------------------------------------------------------------------
     *
     * @name   CaseReferral Store
     * @route  POST /api/v1/case-referrals
     *
     * @description
     * Creates a new case referral and flushes related cache tags.
     *
     * @bodyParams (StoreCaseReferralRequest)
     * - beneficiary_case_id   (int|required)     Related beneficiary case
     * - service_id            (int|required)     Requested service
     * - receiver_entity_id    (int|required)     Entity assigned to deliver the service
     * - referral_type         (string|required)  Referral type enum
     * - direction             (string|required)  Referral direction enum
     * - status                (string|required)  Initial referral status enum
     * - urgency_level         (string|nullable)  Urgency level enum
     * - reason                (string|nullable)  Reason for referral
     * - notes                 (string|nullable)  Additional notes
     * - referral_date         (date|required)    Referral creation date
     *
     * @return
     * Newly created CaseReferral JSON resource.
     */
    Route::post('/', [CaseReferralController::class, 'store'])
        ->name('case-referrals.store');


    /*
     *----------------------------------------------------------------------
     * 3. Get Case Referral Profile
     * ----------------------------------------------------------------------
     *
     * @name   CaseReferral Show
     * @route  GET /api/v1/case-referrals/{case_referral}
     *
     * @description
     * Returns full details of a single case referral record.
     *
     * @urlParams
     * - case_referral  (int)  The ID of the case referral
     *
     * @return
     * CaseReferral JSON resource including service, beneficiary case, and receiver entity relations.
     */
    Route::get('{case_referral}', [CaseReferralController::class, 'show'])
        ->name('case-referrals.show');

    /*
     * ----------------------------------------------------------------------
     * 4. Full/Partial Update
     * ----------------------------------------------------------------------
     *
     * @name   CaseReferral Update
     * @route  PUT /api/v1/case-referrals/{case_referral}
     *
     * @description
     * Updates an existing case referral record and flushes related cache tags.
     *
     * @bodyParams (UpdateCaseReferralRequest)
     * - beneficiary_case_id   (int|nullable)
     * - service_id            (int|nullable)
     * - receiver_entity_id    (int|nullable)
     * - referral_type         (string|nullable)
     * - direction             (string|nullable)
     * - status                (string|nullable)
     * - urgency_level         (string|nullable)
     * - reason                (string|nullable)
     * - notes                 (string|nullable)
     * - referral_date         (date|nullable)
     * - accepted_at           (datetime|nullable)
     * - completed_at          (datetime|nullable)
     * - rejected_at           (datetime|nullable)
     * - rejection_reason      (string|nullable)
     * - cancelled_at          (datetime|nullable)
     * - cancellation_reason   (string|nullable)
     *
     * @return
     * Updated CaseReferral JSON resource.
     */
    Route::put('{case_referral}', [CaseReferralController::class, 'update'])
        ->name('case-referrals.update');

    /*
     * ----------------------------------------------------------------------
     * 5. Delete Case Referral
     * ----------------------------------------------------------------------
     *
     * @name   CaseReferral Delete
     * @route  DELETE /api/v1/case-referrals/{case_referral}
     *
     * @description
     * Soft or permanent deletes a case referral record and flushes relevant cache tags.
     *
     * @urlParams
     * - case_referral  (int)  The ID of the case referral
     */
    Route::delete('{case_referral}', [CaseReferralController::class, 'destroy'])
        ->name('case-referrals.destroy');

    /*
     * ----------------------------------------------------------------------
     * 6. Update Referral Status
     * ----------------------------------------------------------------------
     *
     * @name   CaseReferral Update Status
     * @route  PUT /api/v1/case-referrals/{case_referral}/updateStatus
     *
     * @description
     * Updates the status lifecycle fields (accepted, rejected, completed, cancelled)
     * of a referral and flushes related cache tags.
     *
     * @bodyParams
     * - status                (string|required)  New referral status
     * - accepted_at           (datetime|nullable)
     * - completed_at          (datetime|nullable)
     * - rejected_at           (datetime|nullable)
     * - rejection_reason      (string|nullable)
     * - cancelled_at          (datetime|nullable)
     * - cancellation_reason   (string|nullable)
     *
     * @return
     * Updated CaseReferral JSON resource.
     */
    Route::put('{case_referral}/updateStatus', [CaseReferralController::class, 'updateStatus'])
        ->name('case-referrals.updateStatus');
    });
