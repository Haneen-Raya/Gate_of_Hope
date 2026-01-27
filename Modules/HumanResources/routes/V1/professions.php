<?php

use Illuminate\Support\Facades\Route;
use Modules\HumanResources\Http\Controllers\Api\V1\ProfessionController;

/*
|--------------------------------------------------------------------------
| Profession Management Module - API V1
|--------------------------------------------------------------------------
| Controller: ProfessionController
| Model: Profession
| Base Path: /api/v1/professions
|--------------------------------------------------------------------------
*/

Route::prefix('professions')->group(function () {

    /**
     * @name 1. List & Search Professions
     * @path GET /api/v1/professions
     * * @query_params:
     * - @param name (string): Partial search by profession nomenclature.
     * - @param code (string): Exact match search by unique system code.
     * - @param is_active (bool): Filter by operational status (0/1).
     * - @param page (int): Pagination page number (default: 1).
     * * @features: Tagged Caching (professions), Dynamic Scopes via ProfessionBuilder.
     */
    Route::get('/', [ProfessionController::class, 'index'])
        ->name('professions.index');

    /**
     * @name 2. Store New Profession
     * @path POST /api/v1/professions
     * * @body_payload (StoreProfessionRequest):
     * - name (string/required): The title of the profession.
     * - is_active (bool/optional): Initial status (default: 1).
     * * @description Registers a new profession, automatically derives a 4-character 
     * unique code from the name, and flushes global list caches.
     */
    Route::post('/', [ProfessionController::class, 'store'])
        ->name('professions.store');

    /**
     * @name 3. Get Profession Details
     * @path GET /api/v1/professions/{profession}
     * * @url_params:
     * - profession (int): The unique ID of the profession record.
     * * @features: Dual-Layer Caching (Global + Specific Resource Tag).
     * @return Full JSON object with associated counts (trainers/activities).
     */
    Route::get('{profession}', [ProfessionController::class, 'show'])
        ->name('professions.show');

    /**
     * @name 4. Full/Partial Update
     * @path PUT /api/v1/professions/{profession}
     * * @description Updates the profession record. If the name is changed, the system 
     * re-calculates the code. Triggers "Ripple Effect" invalidation for all related tags.
     */
    Route::put('{profession}', [ProfessionController::class, 'update'])
        ->name('professions.update');

    /**
     * @name 5. Delete Profession
     * @path DELETE /api/v1/professions/{profession}
     * * @description Removes the profession from the system. Note: This may be 
     * restricted if the profession is linked to active specialists or trainers.
     */
    Route::delete('{profession}', [ProfessionController::class, 'destroy'])
        ->name('professions.destroy');
});