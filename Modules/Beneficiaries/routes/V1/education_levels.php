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
|--------------------------------------------------------------------------
*/

Route::prefix('education-levels')->group(function () {

    /**
     * @name 1. List & Search education levels
     * @path GET /api/v1/education-levels
     *
     * @query_params:
     * - @param name (string): Filter by name.
     * - @param is_active (bool): Filter by activtion status (0/1).
     * - @param page (int): Pagination page number (default: 1).
     *
     * @features: Tagged Caching, Dynamic Scopes, Custom Builder.
     */
    Route::get('/', [EducationLevelController::class, 'index'])
        ->name('education-levels.index');

    /**
     * @name 2. Store New Education Level
     * @path POST /api/v1/education-levels
     *
     * @body_payload (EducationLevelRequest):
     * - name (string/required/unique): name of the Education Level.
     * - is_active (bool/nullable): activation state for the education level , it is true by defualtt.
     *
     * @description Persists a new education level,and invalidates global list cache.
     */
    Route::post('/', [EducationLevelController::class, 'store'])
        ->name('education-levels.store');

    /**
     * @name 3. Get Education Level Profile
     * @path GET /api/v1/education-levels/{education_level}
     *
     * @url_params:
     * - education_level (int): The ID of the education_level.
     *
     * @return Full JSON object.
     */
    Route::get('{education_level}', [EducationLevelController::class, 'show'])
        ->name('education-levels.show');

    /**
     * @name 4. Full/Partial Update
     * @path PUT /api/v1/education-levels/{education_level}
     *
     * @description Updates the education_level record and purges all related cache tags
     * to prevent stale data in the list view (Ripple Effect Invalidation).
     */
    Route::put('{education_level}', [EducationLevelController::class, 'update'])
        ->name('education-levels.update');

    /**
     * @name 5. Delete Education Level
     * @path DELETE /api/v1/education-levels/{education_level}
     *
     * @description Permanently or Soft deletes the record. Triggers cache flush
     * for the specific resource and all paginated lists.
     */
    Route::delete('{education_level}', [EducationLevelController::class, 'destroy'])
        ->name('education-levels.destroy');

    /**
     * @name 4. Update the Activation state for the education level
     * @path PUT /api/v1/education-levels/{education_level}
     *
     * @description Updates the education_level record and purges all related cache tags
     * to prevent stale data in the list view (Ripple Effect Invalidation).
     */
    Route::put('{education_level}/updateActivation', [EducationLevelController::class, 'updateActivation'])
        ->name('education-levels.updateActivation');
});
