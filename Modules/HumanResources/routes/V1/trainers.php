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
     */
    Route::get('trainers',[TrainerController::class, 'index'])
        ->name('trainers.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Trainer (Self Apply)
     * ----------------------------------------------------------------------
     */
    Route::post('trainers',[TrainerController::class, 'store'])
        ->name('trainers.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Trainer Profile
     * ----------------------------------------------------------------------
     */
    Route::get('trainers/{trainer}',[TrainerController::class, 'show'])
        ->whereNumber('trainer')
        ->name('trainers.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Trainer
     * ----------------------------------------------------------------------
     */
    Route::put('trainers/{trainer}',[TrainerController::class, 'update'])
        ->whereNumber('trainer')
        ->name('trainers.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Trainer
     * ----------------------------------------------------------------------
     */
    Route::delete('trainers/{trainer}',[TrainerController::class, 'destroy'])
        ->whereNumber('trainer')
        ->name('trainers.destroy');

    /**
     * ----------------------------------------------------------------------
     * 6. Approve Trainer (Admin Only)
     * ----------------------------------------------------------------------
     * @name trainers.approve
     * @path POST /api/v1/human-resources/trainers/{trainer}/approve
     *
     * @description:
     * - Changes trainer status to APPROVED
     * - Assigns trainer role to user
     * - Sends approval notification
     */
    Route::post('trainers/{trainer}/approve',[TrainerController::class, 'approve'])
        ->whereNumber('trainer')
        ->name('trainers.approve');

    /**
     * ----------------------------------------------------------------------
     * 7. Reject Trainer (Admin Only)
     * ----------------------------------------------------------------------
     * @name trainers.reject
     * @path POST /api/v1/human-resources/trainers/{trainer}/reject
     *
     * @description:
     * - Changes trainer status to REJECTED
     * - Optional rejection reason
     */
    Route::post('trainers/{trainer}/reject',[TrainerController::class, 'reject'])
        ->whereNumber('trainer')
        ->name('trainers.reject');

});
