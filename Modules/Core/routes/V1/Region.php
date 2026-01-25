<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\V1\RegionController;

/*
|--------------------------------------------------------------------------
| Region Management - API V1 Routes
|--------------------------------------------------------------------------
| File Path: routes/V1/Region.php
| Controller: RegionController
| Purpose: Handles all region-related CRUD operations and caching logic.
| Included in: routes/api.php
|--------------------------------------------------------------------------
*/

Route::prefix('v1/regions')->group(function () {

    /**
     * @name 1. List Active Regions
     * @path GET /api/v1/regions
     * * @query_params:
     * - @param page (int): Pagination page number (default: 1).
     * - @param per_page (int): Items per page (default: 15).
     * * @features: Tagged Caching (Redis), Active Scope Filtering, Latest First.
     */
    Route::get('/', [RegionController::class, 'index']);

    /**
     * @name 2. List Inactive Regions
     * @path GET /api/v1/regions/inactive
     * * @description Retrieves regions where is_active is false.
     * @features: Uses Custom 'inactive' Eloquent Scope, Redis Tagged Caching.
     */
    Route::get('/inactive', [RegionController::class, 'inactiveIndex']);

    /**
     * @name 3. Store New Region
     * @path POST /api/v1/regions
     * * @body_payload (StoreRegionRequest):
     * - name (string/required/unique): The full name of the region.
     * - label (string/nullable): Internal label (auto-formatted to Uppercase).
     * - location (string/nullable): Coordinates or description.
     * - is_active (bool/nullable): Status of the region.
     * - code (string/nullable): If empty, auto-generated (e.g., Damascus -> DAM).
     * * @features: Observer Auto-generation, Mutator Uppercasing, Cache Invalidation.
     */
    Route::post('/', [RegionController::class, 'store']);

    /**
     * @name 4. Get Region Details
     * @path GET /api/v1/regions/{id}
     * * @url_params:
     * - id (int): The unique identifier of the region.
     * * @features: Individual Tagged Cache (region_{id}), 1-Hour TTL.
     */
    Route::get('/{id}', [RegionController::class, 'show']);

    /**
     * @name 5. Update Region
     * @path PUT /api/v1/regions/{region}
     * * @body_payload (UpdateRegionRequest):
     * - @param name: Unique except current ID.
     * - @param code: Unique except current ID.
     * * @description Updates record and triggers AutoFlushCache trait to purge
     * both specific and global cache tags.
     */
    Route::put('/{region}', [RegionController::class, 'update']);

    /**
     * @name 6. Delete Region
     * @path DELETE /api/v1/regions/{region}
     * * @description Hard delete of the region record.
     * Triggers immediate cache flush via AutoFlushCache trait.
     */
    Route::delete('/{region}', [RegionController::class, 'destroy']);
});
