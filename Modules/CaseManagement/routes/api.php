<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\CaseManagementController;

/**
 * Case Management Domain - Version 1
 * * Global Protection: All routes require Sanctum Authentication
 * and Locale setting for multilingual clinical reports.
 */

Route::middleware(['auth:sanctum', 'set_locale_lang'])->prefix('v1')->group(function () {

    /** * Case Sessions:
     * Handles psychological and social work session logs.
     */
    require __DIR__ . '/v1/case_sessions.php';

    /** * Master Case Resource:
     * Provides CRUD operations for the central Case Management entity.
     */
    Route::apiResource('casemanagements', CaseManagementController::class)->names('casemanagement');

    /** * Clinical Timeline:
     * Manages Case Events (Significant occurrences) and Support Plans.
     */
    require __DIR__ . '/V1/case-events.php';
    require __DIR__ . '/V1/case-support-plans.php';

    /** * Goal Setting & Monitoring:
     * Endpoints for defining, tracking, and updating case goals.
     */
    require __DIR__ . '/V1/case-plan-goals.php';

    /** * Evaluation & Feedback:
     * Manages periodic Case Reviews to assess progress.
     */
    require __DIR__ . '/V1/case-reviews.php';

    /** * External Referrals & Service Mapping:
     * Handles out-referrals to other entities and available service catalogs.
     */
    require __DIR__ . '/V1/case-referrals.php';
    require __DIR__ . '/V1/services.php';

    /** * Beneficiary Mapping:
     * Logic for linking specific beneficiaries to their management cases.
     */
    require __DIR__ . '/V1/beneficiary-case.php';
});
