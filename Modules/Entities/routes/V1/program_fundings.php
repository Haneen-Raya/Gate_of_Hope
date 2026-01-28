<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\Api\V1\ProgramFundingController;

/*
|--------------------------------------------------------------------------
| Program Funding Management - API V1
|--------------------------------------------------------------------------
| Controller: ProgramFundingController
| Model: ProgramFunding
| Base Path: /api/v1/program-fundings
|--------------------------------------------------------------------------
*/

Route::prefix('program-fundings')->group(function () {

    /**
     * @name 1. List & Search Program Fundings
     * @path GET /api/v1/program-fundings
     *
     * @query_params:
     * - @param program_id (int): Filter by a specific program ID.
     * - @param donor_entity_id (int): Filter by a specific donor entity ID.
     * - @param currency (string): Filter by program funding currency.
     * - @param start_date (date): Filter fundings starting on/after YYYY-MM-DD.
     * - @param end_date (date): Filter fundings ending before/on YYYY-MM-DD.
     * - @param min_amount (int): Filter fundings by min amount.
     * - @param max_amount (int): Filter fundings by max amount.
     * - @param page (int): Pagination page number (default: 1).
     *
     * @features: Deterministic Tagged Caching, MD5 Signature Key, Custom Query Builder.
     */
    Route::get('/', [ProgramFundingController::class, 'index']);

    /**
     * @name 2. Store New Program Funding
     * @path POST /api/v1/program-fundings
     *
     * @body_payload (StoreProgramFundingRequest):
     * - program_id (int/required): The program this funding belongs to.
     * - donor_entity_id (int/required): The donor entity this funding belongs to.
     * - start_date (date/required): Commencement of the funding (>= today).
     * - end_date (date/required): Completion of the funding (> start_date).
     * - currency (string/required): The currency of this funding.
     * - amount (int/required): The amount of this funding.
     *
     * @description Persists a new funding,and invalidates global list cache.
     */
    Route::post('/', [ProgramFundingController::class, 'store']);

    /**
     * @name 3. Get Program Funding Profile
     * @path GET /api/v1/program-fundings/{program_funding}
     *
     * @url_params:
     * - program_funding (Program Funding): The object of the Program Funding.
     *
     * @return Full JSON object with goals and audit metadata.
     *
     * @note: Uses ID-based retrieval to maximize Service Layer Cache Hits.
     */
    Route::get('{program_funding}', [ProgramFundingController::class, 'show']);

    /**
     * @name 4. Full/Partial Update
     * @path PUT /api/v1/program-fundings-plans/{program_funding}
     *
     * @description Updates referral attributes and purges dual-tag cache:
     * 1. Individual record tag (program_funding_{id})
     * 2. Global list tag (program_fundings)
     */
    Route::put('{program_funding}', [ProgramFundingController::class, 'update']);

    /**
     * @name 5. Delete Program Funding
     * @path DELETE /api/v1/program-fundings-plans/{program_funding}
     *
     * @description Permanently or Soft deletes the record. Triggers cache flush
     * for the specific resource and all paginated lists.
     */
    Route::delete('{program_funding}', [ProgramFundingController::class, 'destroy']);
});
