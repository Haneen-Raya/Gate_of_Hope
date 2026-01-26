<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\BeneficiaryCaseController;

/*
|--------------------------------------------------------------------------
| Beneficiary Case Management - API V1 Routes
|--------------------------------------------------------------------------
| File Path: Modules/CaseManagement/Routes/V1/case.php
| Controller: BeneficiaryCaseController
| Purpose: إدارة دورة حياة الحالات (فتح، تحديث، إغلاق، وحذف) مع الكاش.
|--------------------------------------------------------------------------
*/

Route::prefix('v1/cases')->group(function () {

    /**
     * @name 1. List All Cases
     * @path GET /api/v1/cases
     * * @query_parameters:
     * - beneficiary_id: (int) Filter by a specific beneficiary.
     * - sub_issue_id: (int) Filter by specific sub-issue.
     * - case_manager_id: (int) Filter by assigned case manager.
     * - region_id: (int) Filter cases within a specific region.
     * - status: (string) Filter by CaseStatus (open, closed, etc.).
     * - priority: (string) Filter by priority level (High, Low, etc.).
     * - opened_from / opened_to: (date) Range for case opening date.
     * - closed_from / closed_to: (date) Range for case closure date.
     * - beneficiary_columns: (string/array) Specific columns to load for the beneficiary (e.g., id,gender).
     * * @features:
     * - Tagged Caching (cases_global)
     * - Pagination (Default 15 per page)
     * - Dynamic Filtering via BeneficiaryCaseBuilder
     */
    Route::get('/', [BeneficiaryCaseController::class, 'index']);
    Route::get('/', [BeneficiaryCaseController::class, 'index']);

    /**
     * @name 2. Store New Case
     * @path POST /api/v1/cases
     * @features:
     * - Auto-assignment via HasAuditUsers (If enabled)
     * - Immediate Cache Invalidation for global list.
     */
    Route::post('/', [BeneficiaryCaseController::class, 'store']);

    /**
     * @name 3. Get Case Details
     * @path GET /api/v1/cases/{id}
     * @features:
     * - Specific Tagged Cache (case_{id})
     * - Relationship Loading (Beneficiary, Manager, Region)
     */
    Route::get('/{id}', [BeneficiaryCaseController::class, 'show']);

    /**
     * @name 4. Update Case
     * @path PUT /api/v1/cases/{case}
     * @features:
     * - Partial Updates (Priority, Status, Closure)
     * - AutoFlushCache: Purges both global and specific tags.
     * - LogsActivity: Records all changes in audit logs.
     */
    Route::put('/{case}', [BeneficiaryCaseController::class, 'update']);

    /**
     * @name 5. Delete Case
     * @path DELETE /api/v1/cases/{case}
     * @features:
     * - Soft Deletes (if enabled in Model)
     * - Immediate Cache Flush.
     */
    Route::delete('/{case}', [BeneficiaryCaseController::class, 'destroy']);
});
