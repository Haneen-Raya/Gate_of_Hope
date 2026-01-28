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
|
| This module manages program fundings and their association with donor entities.
| Supports filtering by amount, date, program, and currency.
|--------------------------------------------------------------------------
*/

Route::prefix('program-fundings')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Filter Program Fundings
     * ----------------------------------------------------------------------
     *
     * @name   Program Funding Index
     * @route  GET /api/v1/program-fundings
     *
     * @description
     * Returns a paginated list of program funding records.
     * Supports dynamic filtering using ProgramFundingBuilder.
     *
     * @queryParams
     * - program_id       (int|null)
     *      Filter by a specific program ID.
     *
     * - donor_entity_id  (int|null)
     *      Filter by a specific donor entity ID.
     *
     * - currency         (string|null)
     *      Filter by funding currency (e.g., USD, EUR).
     *
     * - start_date       (date|null)
     *      Filter fundings starting on or after this date (YYYY-MM-DD).
     *
     * - end_date         (date|null)
     *      Filter fundings ending before or on this date (YYYY-MM-DD).
     *
     * - min_amount       (int|null)
     *      Filter fundings with amount >= min_amount.
     *
     * - max_amount       (int|null)
     *      Filter fundings with amount <= max_amount.
     *
     * - page             (int)
     *      Pagination page number (default: 1).
     *
     * @features
     * - Deterministic Tagged Caching
     * - Custom Query Builder Filtering
     * - Pagination Support
     */
    Route::get('/', [ProgramFundingController::class, 'index']);

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Program Funding
     * ----------------------------------------------------------------------
     *
     * @name   Program Funding Store
     * @route  POST /api/v1/program-fundings
     *
     * @description
     * Persists a new program funding record and invalidates global list cache.
     *
     * @bodyParams (StoreProgramFundingRequest)
     * - program_id       (int|required)
     *      The program associated with this funding.
     *
     * - donor_entity_id  (int|required)
     *      The donor entity providing the funding.
     *
     * - start_date       (date|required)
     *      Funding commencement date (>= today).
     *
     * - end_date         (date|required)
     *      Funding completion date (> start_date).
     *
     * - currency         (string|required)
     *      Funding currency.
     *
     * - amount           (int|required)
     *      Funding amount.
     *
     * @return
     * Newly created ProgramFunding JSON resource.
     */
    Route::post('/', [ProgramFundingController::class, 'store']);

    /**
     * ----------------------------------------------------------------------
     * 3. Get Program Funding Details
     * ----------------------------------------------------------------------
     *
     * @name   Program Funding Show
     * @route  GET /api/v1/program-fundings/{program_funding}
     *
     * @description
     * Retrieves a single program funding record by its ID.
     * * Includes associated program, donor entity, and audit metadata.
     *
     * @urlParams
     * - program_funding (int|required)
     *      Program Funding ID.
     *
     * @return
     * Full JSON object including metadata and related goals.
     */
    Route::get('{program_funding}', [ProgramFundingController::class, 'show']);

    /**
     * ----------------------------------------------------------------------
     * 4. Update Program Funding
     * ----------------------------------------------------------------------
     *
     * @name   Program Funding Update
     * @route  PUT /api/v1/program-fundings/{program_funding}
     *
     * @description
     * Updates an existing program funding record (full or partial update).
     * Flushes cache tags:
     * 1. Individual record (program_funding_{id})
     * 2. Global list (program_fundings)
     *
     * @urlParams
     * - program_funding (int|required)
     *
     * @bodyParams
     * - program_id       (int|nullable)
     * - donor_entity_id  (int|nullable)
     * - start_date       (date|nullable)
     * - end_date         (date|nullable)
     * - currency         (string|nullable)
     * - amount           (int|nullable)
     *
     * @return
     * Updated ProgramFunding JSON resource.
     */
    Route::put('{program_funding}', [ProgramFundingController::class, 'update']);

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Program Funding
     * ----------------------------------------------------------------------
     *
     * @name   Program Funding Delete
     * @route  DELETE /api/v1/program-fundings/{program_funding}
     *
     * @description
     * Permanently or soft deletes a program funding record.
     * Flushes cache for both the specific record and paginated lists.
     *
     * @urlParams
     * - program_funding (int|required)
     *
     * @return
     * Success response message.
     */
    Route::delete('{program_funding}', [ProgramFundingController::class, 'destroy']);
});
