<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\Api\V1\EducationLevelController;

/*
|--------------------------------------------------------------------------
| Education Level Management Module - API V1
|--------------------------------------------------------------------------
| Controller: EducationLevelController
| Model: EducationLevel
| Base Path: /api/v1/education-levels
|
| This module manages education level reference data
| used inside beneficiary social background records.
|
|--------------------------------------------------------------------------
*/

Route::prefix('education-levels')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Filter Education Levels
     * ----------------------------------------------------------------------
     *
     * @name   Education Level Index
     * @route  GET /api/v1/education-levels
     *
     * @description
     * Returns a paginated list of education levels.
     * Supports searching and filtering through EducationLevelBuilder.
     *
     * @queryParams
     * - term (string|null)
     *      Search by education level name (LIKE %term%).
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
    Route::get('/', [EducationLevelController::class, 'index'])
        ->name('education-levels.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Education Level
     * ----------------------------------------------------------------------
     *
     * @name   Education Level Store
     * @route  POST /api/v1/education-levels
     *
     * @description
     * Creates a new education level record.
     * Automatically invalidates cached list data (Ripple Effect).
     *
     * @bodyParams (StoreEducationLevelRequest)
     * - name (string|required|unique)
     *      The education level name.
     *
     * - is_active (bool|nullable)
     *      Activation state (default: true).
     *
     * @return
     * Newly created EducationLevel JSON resource.
     */
    Route::post('/', [EducationLevelController::class, 'store'])
        ->name('education-levels.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Education Level Details
     * ----------------------------------------------------------------------
     *
     * @name   Education Level Show
     * @route  GET /api/v1/education-levels/{education_level}
     *
     * @description
     * Retrieves a single education level record by its ID.
     *
     * @urlParams
     * - education_level (int|required)
     *      Education level ID.
     *
     * @return
     * Full EducationLevel JSON object.
     */
    Route::get('{education_level}', [EducationLevelController::class, 'show'])
        ->name('education-levels.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Education Level
     * ----------------------------------------------------------------------
     *
     * @name   Education Level Update
     * @route  PUT /api/v1/education-levels/{education_level}
     *
     * @description
     * Updates an existing education level record (full or partial update).
     * Flushes all related cache tags to prevent stale list/detail responses.
     *
     * @urlParams
     * - education_level (int|required)
     *
     * @bodyParams
     * - name (string|nullable)
     * - is_active (bool|nullable)
     *
     * @return
     * Updated EducationLevel JSON resource.
     */
    Route::put('{education_level}', [EducationLevelController::class, 'update'])
        ->name('education-levels.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Education Level
     * ----------------------------------------------------------------------
     *
     * @name   Education Level Delete
     * @route  DELETE /api/v1/education-levels/{education_level}
     *
     * @description
     * Deletes an education level record (soft/permanent depending on setup).
     * Triggers cache invalidation for both the resource and list caches.
     *
     * @urlParams
     * - education_level (int|required)
     *
     * @return
     * Success response message.
     */
    Route::delete('{education_level}', [EducationLevelController::class, 'destroy'])
        ->name('education-levels.destroy');

     /**
     * ----------------------------------------------------------------------
     * 6. Update Education Level Activation State
     * ----------------------------------------------------------------------
     *
     * @name   Education Level Activation Update
     * @route  PUT /api/v1/education-levels/{education_level}/updateActivation
     *
     * @description
     * Updates only the activation status of an education level.
     * Useful for enabling/disabling reference values without deletion.
     *
     * Automatically flushes cache tags (Ripple Effect).
     *
     * @urlParams
     * - education_level (int|required)
     *
     * @bodyParams
     * - is_active (bool|required)
     *      New activation state (true/false).
     *
     * @return
     * Updated EducationLevel JSON resource.
     */
    Route::put('{education_level}/updateActivation', [EducationLevelController::class, 'updateActivation'])
        ->name('education-levels.updateActivation');
});
