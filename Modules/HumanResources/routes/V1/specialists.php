<?php

use Illuminate\Support\Facades\Route;
use Modules\HumanResources\Http\Controllers\Api\V1\SpecialistController;

/*
|--------------------------------------------------------------------------
| Human Resources Module - API V1
|--------------------------------------------------------------------------
| Controller: SpecialistController
| Model: Specialist
| Base Path: /api/v1/human-resources
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('human-resources')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List Specialists (Paginated)
     * ----------------------------------------------------------------------
     * @name specialists.index
     * @path GET /api/v1/human-resources/specialists
     *
     * @query_params:
     * - issue_category_id (int): Filter by issue category
     * - gender (enum): male | female
     * - is_active (bool)
     * - page (int)
     *
     * @features:
     * - Custom Builder
     * - Tagged Caching
     */
    Route::get('specialists',[SpecialistController::class, 'index'])->name('specialists.index');
    /**
     * ----------------------------------------------------------------------
     * 2. Store New Specialist
     * ----------------------------------------------------------------------
     * @name specialists.store
     * @path POST /api/v1/human-resources/specialists
     *
     * @body_payload (StoreSpecialistRequest):
     * - user_id (int/required)
     * - gender (enum/required)
     * - date_of_birth (date/required)
     * - issue_category_id (int/required)
     *
     * @description:
     * Creates a specialist and invalidates specialists cache.
     */
    Route::post('specialists',[SpecialistController::class, 'store'])->name('specialists.store');
    /**
     * ----------------------------------------------------------------------
     * 3. Show Specialist Profile
     * ----------------------------------------------------------------------
     * @name specialists.show
     * @path GET /api/v1/human-resources/specialists/{specialist}
     *
     * @url_params:
     * - specialist (int): Specialist ID
     *
     * @features:
     * - Route Model Binding
     */
    Route::get('specialists/{specialist}',[SpecialistController::class, 'show'])->whereNumber('specialist')->name('specialists.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Specialist
     * ----------------------------------------------------------------------
     * @name specialists.update
     * @path PUT /api/v1/human-resources/specialists/{specialist}
     *
     * @body_payload (UpdateSpecialistRequest):
     * - gender (sometimes)
     * - date_of_birth (sometimes)
     * - issue_category_id (sometimes)
     *
     * @description:
     * Updates specialist and clears cache.
     */
    Route::put('specialists/{specialist}',[SpecialistController::class, 'update'])->whereNumber('specialist')->name('specialists.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Specialist
     * ----------------------------------------------------------------------
     * @name specialists.destroy
     * @path DELETE /api/v1/human-resources/specialists/{specialist}
     *
     * @description:
     * Soft delete (if enabled) and invalidate cache.
     */
    Route::delete('specialists/{specialist}',[SpecialistController::class, 'destroy'])->whereNumber('specialist') ->name('specialists.destroy');

});
