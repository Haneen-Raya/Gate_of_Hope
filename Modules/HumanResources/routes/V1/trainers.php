<?php

use Illuminate\Support\Facades\Route;
use Modules\HumanResources\Http\Controllers\Api\V1\TrainerController;

/*
|--------------------------------------------------------------------------
| Human Resources Module - API V1
|--------------------------------------------------------------------------
| Controller: TrainerController
| Model: Trainer
| Base Path: /api/v1/human-resources
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('human-resources')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List Trainers (Paginated)
     * ----------------------------------------------------------------------
     * @name trainers.index
     * @path GET /api/v1/human-resources/trainers
     *
     * @query_params:
     * - profession_id (int): Filter by profession
     * - gender (enum): male | female
     * - is_external (bool)
     * - page (int)
     *
     * @features:
     * - Custom Builder
     * - Tagged Caching
     */
    Route::get('trainers',[TrainerController::class, 'index'])
        ->name('trainers.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Trainer
     * ----------------------------------------------------------------------
     * @name trainers.store
     * @path POST /api/v1/human-resources/trainers
     *
     * @body_payload (StoreTrainerRequest):
     * - user_id (int/required)
     * - profession_id (int/required)
     * - gender (enum/required)
     * - date_of_birth (date/required)
     * - bio (string/nullable)
     * - certification_level (string/required)
     * - hourly_rate (decimal/required)
     * - is_external (bool/required)
     *
     * @description:
     * Creates a trainer and invalidates trainers cache.
     */
    Route::post('trainers',[TrainerController::class, 'store'])
        ->name('trainers.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Trainer Profile
     * ----------------------------------------------------------------------
     * @name trainers.show
     * @path GET /api/v1/human-resources/trainers/{trainer}
     *
     * @url_params:
     * - trainer (int): Trainer ID
     *
     * @features:
     * - Route Model Binding
     */
    Route::get('trainers/{trainer}',[TrainerController::class, 'show'])
        ->whereNumber('trainer')
        ->name('trainers.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Trainer
     * ----------------------------------------------------------------------
     * @name trainers.update
     * @path PUT /api/v1/human-resources/trainers/{trainer}
     *
     * @body_payload (UpdateTrainerRequest):
     * - profession_id (sometimes)
     * - gender (sometimes)
     * - date_of_birth (sometimes)
     * - bio (sometimes)
     * - certification_level (sometimes)
     * - hourly_rate (sometimes)
     * - is_external (sometimes)
     *
     * @description:
     * Updates trainer and clears cache.
     */
    Route::put('trainers/{trainer}',[TrainerController::class, 'update'])
        ->whereNumber('trainer')
        ->name('trainers.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Trainer
     * ----------------------------------------------------------------------
     * @name trainers.destroy
     * @path DELETE /api/v1/human-resources/trainers/{trainer}
     *
     * @description:
     * Soft delete (if enabled) and invalidate cache.
     */
    Route::delete('trainers/{trainer}',[TrainerController::class, 'destroy'])
        ->whereNumber('trainer')
        ->name('trainers.destroy');

});
