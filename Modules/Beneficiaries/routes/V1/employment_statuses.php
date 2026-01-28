<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\Api\V1\EmploymentStatusController;

/*
|--------------------------------------------------------------------------
| Employment Status Management Module - API V1
|--------------------------------------------------------------------------
| Controller: EmploymentStatusController
| Model: EmploymentStatus
| Base Path: /api/v1/employment-statuses
|
| This module manages employment status reference data
| used inside beneficiary social background records.
|--------------------------------------------------------------------------
*/

Route::prefix('employment-statuses')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Filter Employment Statuses
     * ----------------------------------------------------------------------
     *
     * @name   Employment Status Index
     * @route  GET /api/v1/employment-statuses
     *
     * @description
     * Returns a paginated list of employment statuses.
     * Supports searching and dynamic filtering using EmploymentStatusBuilder.
     *
     * @queryParams
     * - term (string|null)
     *      Search by employment status name (LIKE %term%).
     *
     * - is_active (bool|null)
     *      Filter by activation status (0 or 1).
     *
     * - page (int)
     *      Pagination page number (default: 1).
     *
     * @features
     * - Custom Query Builder Filtering
     * - Tagged Caching Support
     * - Activity Logging
     */
    Route::get('/', [EmploymentStatusController::class, 'index'])
        ->name('employment-statuses.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Employment Status
     * ----------------------------------------------------------------------
     *
     * @name   Employment Status Store
     * @route  POST /api/v1/employment-statuses
     *
     * @description
     * Creates a new employment status record.
     * Automatically invalidates cached lists (Ripple Effect).
     *
     * @bodyParams (StoreEmploymentStatusRequest)
     * - name (string|required|unique)
     *      The employment status name.
     *
     * - is_active (bool|nullable)
     *      Activation state (default: true).
     *
     * @return
     * Newly created EmploymentStatus JSON resource.
     */
    Route::post('/', [EmploymentStatusController::class, 'store'])
        ->name('employment-statuses.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Employment Status Details
     * ----------------------------------------------------------------------
     *
     * @name   Employment Status Show
     * @route  GET /api/v1/employment-statuses/{employment_status}
     *
     * @description
     * Retrieves a single employment status record by its ID.
     *
     * @urlParams
     * - employment_status (int|required)
     *      Employment status ID.
     *
     * @return
     * Full EmploymentStatus JSON object.
     */
    Route::get('{employment_status}', [EmploymentStatusController::class, 'show'])
        ->name('employment-statuses.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Employment Status
     * ----------------------------------------------------------------------
     *
     * @name   Employment Status Update
     * @route  PUT /api/v1/employment-statuses/{employment_status}
     *
     * @description
     * Updates an existing employment status record (full or partial update).
     * Flushes all related cache tags to prevent stale data.
     *
     * @urlParams
     * - employment_status (int|required)
     *
     * @bodyParams
     * - name (string|nullable)
     * - is_active (bool|nullable)
     *
     * @return
     * Updated EmploymentStatus JSON resource.
     */
    Route::put('{employment_status}', [EmploymentStatusController::class, 'update'])
        ->name('employment-statuses.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Employment Status
     * ----------------------------------------------------------------------
     *
     * @name   Employment Status Delete
     * @route  DELETE /api/v1/employment-statuses/{employment_status}
     *
     * @description
     * Deletes an employment status record (soft or permanent depending on model setup).
     * Triggers cache invalidation for the resource and list caches.
     *
     * @urlParams
     * - employment_status (int|required)
     *
     * @return
     * Success response message.
     */
    Route::delete('{employment_status}', [EmploymentStatusController::class, 'destroy'])
        ->name('employment-statuses.destroy');

    /**
     * ----------------------------------------------------------------------
     * 6. Update Employment Status Activation State
     * ----------------------------------------------------------------------
     *
     * @name   Employment Status Activation Update
     * @route  PUT /api/v1/employment-statuses/{employment_status}/updateActivation
     *
     * @description
     * Updates only the activation status of an employment status.
     * Useful for enabling/disabling reference values without deleting them.
     *
     * Automatically purges cache tags (Ripple Effect).
     *
     * @urlParams
     * - employment_status (int|required)
     *
     * @bodyParams
     * - is_active (bool|required)
     *      New activation state (true/false).
     *
     * @return
     * Updated EmploymentStatus JSON resource.
     */
    Route::put('{employment_status}/updateActivation', [EmploymentStatusController::class, 'updateActivation'])
        ->name('employment-statuses.updateActivation');
});
