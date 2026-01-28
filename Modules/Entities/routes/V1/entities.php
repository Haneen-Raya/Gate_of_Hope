<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\Api\V1\EntitiyController;

/*
|--------------------------------------------------------------------------
| Entitiy Management Module - API V1
|--------------------------------------------------------------------------
|
| Controller : EntitiyController
| Model      : Entitiy
| Base Path  : /api/v1/entities
|
| This module manages entities and supports filtering by
| name, code, type, capabilities, and minimum related records.
|
|--------------------------------------------------------------------------
*/

Route::prefix('entities')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Filter Entities
     * ----------------------------------------------------------------------
     *
     * @name   Entitiy Index
     * @route  GET /api/v1/entities
     *
     * @description
     * Returns a paginated list of entities.
     * Supports dynamic filtering using EntityBuilder.
     *
     * @queryParams
     * - user_id                 (int|null)     Filter by owner user ID
     * - name                    (string|null)  Filter by entity name (partial match)
     * - code                    (string|null)  Filter by exact code
     * - entity_type             (string|null)  Filter by entity type
     * - can_provide_services    (bool|null)    Filter by capability to provide services
     * - can_receive_referrals   (bool|null)    Filter by capability to receive referrals
     * - can_fund_programs       (bool|null)    Filter by capability to fund programs
     * - min_case_referrals      (int|null)     Minimum number of related case referrals
     * - min_program_fundings    (int|null)     Minimum number of related program fundings
     * - min_donor_reports       (int|null)     Minimum number of related donor reports
     * - min_activities          (int|null)     Minimum number of related activities
     * - is_active               (bool|null)    Filter by activation status (0/1)
     * - page                    (int)          Pagination page number (default: 1)
     *
     * @features
     * - Tagged Caching
     * - Custom Builder for Dynamic Filtering
     * - Pagination Support
     */
    Route::get('/', [EntitiyController::class, 'index'])
        ->name('entities.index');


    /**
     * ----------------------------------------------------------------------
     * 2. Store New Entity
     * ----------------------------------------------------------------------
     *
     * @name   Entity Store
     * @route  POST /api/v1/entities
     *
     * @description
     * Persists a new entity record and invalidates global list cache.
     *
     * @bodyParams (StoreEntityRequest)
     * - user_id                 (int|required)         The user associated with this entity
     * - name                    (string|required)      Name of the entity
     * - entity_type             (string|required)      Type of the entity
     * - address                 (string|required)      address of the entity
     * - contact_person          (string|required)      contact person of the entity
     * - can_provide_services    (bool|nullable)       Capability to provide services
     * - can_receive_referrals   (bool|nullable)       Capability to receive referrals
     * - can_fund_programs       (bool|nullable)       Capability to fund programs
     * - is_active               (bool|nullable)       Activation state (true by default)
     *
     * @return
     * Newly created Entity JSON resource.
     */
    Route::post('/', [EntitiyController::class, 'store'])
        ->name('entities.store');


    /**
     * ----------------------------------------------------------------------
     * 3. Get Entity Details
     * ----------------------------------------------------------------------
     *
     * @name   Entity Show
     * @route  GET /api/v1/entities/{entitiy}
     *
     * * @description
     * Retrieves a single entitiy record by its ID.
     *
     * @urlParams
     * - entitiy (int|required)  Entitiy ID
     *
     * @return
     * Full JSON object including related activities, case referrals, and program fundings.
     */
    Route::get('{entitiy}', [EntitiyController::class, 'show'])
        ->name('entities.show');


    /**
     * ----------------------------------------------------------------------
     * 4. Full/Partial Update
     * ----------------------------------------------------------------------
     *
     * @name   Entity Update
     * @route  PUT /api/v1/entities/{entitiy}
     *
     * @description
     * Updates an existing entitiy record (full or partial update)
     * and purges all related cache tags to prevent stale data.
     *
     * @bodyParams (UpdateEntityRequest)
     * - user_id                 (int|nullable)
     * - name                    (string|nullable)
     * - code                    (string|nullable)
     * - entity_type             (string|nullable)
     * - can_provide_services    (bool|nullable)
     * - can_receive_referrals   (bool|nullable)
     * - can_fund_programs       (bool|nullable)
     * - is_active               (bool|nullable)
     *
     * @urlParams
     * - entitiy (int|required)
     *
     * @return
     * Updated Entity JSON resource.
     */
    Route::put('{entitiy}', [EntitiyController::class, 'update'])
        ->name('entities.update');


    /**
     * ----------------------------------------------------------------------
     * 5. Delete Entity
     * ----------------------------------------------------------------------
     *
     * @name   Entity Delete
     * @route  DELETE /api/v1/entities/{entitiy}
     *
     * @description
     * Permanently or soft deletes an entitiy record.
     * Flushes cache for the specific entitiy and paginated lists.
     *
     * @urlParams
     * - entitiy (int|required)
     *
     * @return
     * Success response message.
     */
    Route::delete('{entitiy}', [EntitiyController::class, 'destroy'])
        ->name('entities.destroy');


    /**
     * ----------------------------------------------------------------------
     * 6. Update Activation State
     * ----------------------------------------------------------------------
     *
     * @name   Entity Update Activation
     * @route  PUT /api/v1/entities/{entity}/updateActivation
     *
     * @description
     * Updates the activation state of the entity and purges related cache tags.
     *
     * @urlParams
     * - entity (int|required)
     *
     * @bodyParams
     * - is_active (bool|required)
     *
     * @return
     * Updated Entity JSON resource with new activation state.
     */

    /*Route::put('{entity}/updateActivation', [EntitiyController::class, 'updateActivation'])
        ->name('entities.updateActivation');
        */
});
