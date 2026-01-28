<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\Api\V1\HousingTypeController;

/*
|--------------------------------------------------------------------------
| Housing Type Management Module - API V1
|--------------------------------------------------------------------------
| Controller: HousingTypeController
| Model: HousingType
| Base Path: /api/v1/housing-types
|
| This module manages housing type reference data
| used inside beneficiary social background records.
|--------------------------------------------------------------------------
*/

Route::prefix('housing-types')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Filter Housing Types
     * ----------------------------------------------------------------------
     *
     * @name   Housing Type Index
     * @route  GET /api/v1/housing-types
     *
     * @description
     * Returns a paginated list of housing types.
     * Supports searching and dynamic filtering using HousingTypeBuilder.
     *
     * @queryParams
     * - term (string|null)        Search by housing type name (LIKE %term%).
     * - is_active (bool|null)     Filter by activation status (0 or 1).
     *
     * - page (int)                Pagination page number (default: 1).
     *
     * @features
     * - Custom Query Builder Filtering
     * - Tagged Caching Support
     * - Activity Logging
     */
    Route::get('/', [HousingTypeController::class, 'index'])
        ->name('housing-types.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Housing Type
     * ----------------------------------------------------------------------
     *
     * @name   Housing Type Store
     * @route  POST /api/v1/housing-types
     *
     * @description
     * Creates a new housing type record.
     * Automatically invalidates cached lists (Ripple Effect).
     *
     * @bodyParams (StoreHousingTypeRequest)
     * - name (string|required|unique)
     *      The housing type name.
     *
     * - is_active (bool|nullable)
     *      Activation state (default: true).
     *
     * @return
     * Newly created HousingType JSON resource.
     */
    Route::post('/', [HousingTypeController::class, 'store'])
        ->name('housing-types.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Housing Type Details
     * ----------------------------------------------------------------------
     *
     * @name   Housing Type Show
     * @route  GET /api/v1/housing-types/{housing_type}
     *
     * @description
     * Retrieves a single housing type record by its ID.
     *
     * @urlParams
     * - housing_type (int|required)
     *      Housing type ID.
     *
     * @return
     * Full HousingType JSON object.
     */
    Route::get('{housing_type}', [HousingTypeController::class, 'show'])
        ->name('housing-types.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Housing Type
     * ----------------------------------------------------------------------
     *
     * @name   Housing Type Update
     * @route  PUT /api/v1/housing-types/{housing_type}
     *
     * @description
     * Updates an existing housing type record (full or partial update).
     * Flushes all related cache tags to prevent stale data.
     *
     * @urlParams(UpdateHousingTypeRequest)
     * - housing_type (int|required)
     *
     * @bodyParams
     * - name (string|nullable)
     * - is_active (bool|nullable)
     *
     * @return
     * Updated HousingType JSON resource.
     */
    Route::put('{housing_type}', [HousingTypeController::class, 'update'])
        ->name('housing-types.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Housing Type
     * ----------------------------------------------------------------------
     *
     * @name   Housing Type Delete
     * @route  DELETE /api/v1/housing-types/{housing_type}
     *
     * @description
     * Deletes a housing type record (soft or permanent depending on model setup).
     * Triggers cache invalidation for the resource and list caches.
     *
     * @urlParams
     * - housing_type (int|required)
     *
     * @return
     * Success response message.
     */
    Route::delete('{housing_type}', [HousingTypeController::class, 'destroy'])
        ->name('housing-types.destroy');

    /**
     * ----------------------------------------------------------------------
     * 6. Update Housing Type Activation State
     * ----------------------------------------------------------------------
     *
     * @name   Housing Type Activation Update
     * @route  PUT /api/v1/housing-types/{housing_type}/updateActivation
     *
     * @description
     * Updates only the activation status of a housing type.
     * Useful for enabling/disabling reference values without deleting them.
     *
     * Automatically purges cache tags (Ripple Effect).
     *
     * @urlParams
     * - housing_type (int|required)
     *
     * @bodyParams
     * - is_active (bool|required)
     *      New activation state (true/false).
     *
     * @return
     * Updated HousingType JSON resource.
     */
    Route::put('{housing_type}/updateActivation', [HousingTypeController::class, 'updateActivation'])
        ->name('housing-types.updateActivation');
});
