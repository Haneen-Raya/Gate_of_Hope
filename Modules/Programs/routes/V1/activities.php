<?php

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\Api\V1\ActivityController;

/*
|--------------------------------------------------------------------------
| Activity Management Module - API V1
|--------------------------------------------------------------------------
|
| Controller : ActivityController
| Model      : Activity
| Base Path  : /api/v1/activities
|
| Activities represent structured interventions delivered within programs,
| such as training sessions, awareness workshops, or psychosocial support.
|
|--------------------------------------------------------------------------
*/

Route::prefix('activities')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Search Activities
     * ----------------------------------------------------------------------
     *
     * @name   Activity Index
     * @route  GET /api/v1/activities
     *
     * @description
     * Returns a paginated list of activities with dynamic filtering support.
     *
     * @queryParams
     * - is_active              (bool|null)     Filter by activation status (0/1)
     * - program_id             (int|null)      Filter by related program
     * - profession_id          (int|null)      Filter by related profession
     * - provider_entity_id     (int|null)      Filter by provider entity
     * - activity_type          (string|null)   Filter by activity type enum
     * - name                   (string|null)   Search by name (LIKE)
     * - min_activity_sessions  (int|null)      Minimum sessions count
     * - page                   (int)           Pagination page number (default: 1)
     *
     * @features
     * - Custom ActivityBuilder Filters
     * - Tagged Caching
     * - Pagination Support
     */
    Route::get('/', [ActivityController::class, 'index'])
        ->name('activities.index');


    /**
     * ----------------------------------------------------------------------
     * 2. Store New Activity
     * ----------------------------------------------------------------------
     *
     * @name   Activity Store
     * @route  POST /api/v1/activities
     *
     * @description
     * Creates a new activity and flushes related cache tags.
     *
     * @bodyParams (StoreActivityRequest)
     * - program_id           (int|required)      Program owning this activity
     * - profession_id        (int|nullable)      Linked profession domain
     * - provider_entity_id   (int|nullable)      Delivering provider entity
     * - name                 (string|required)   Activity name
     * - description          (string|nullable)   Activity details
     * - activity_type        (string|required)   Enum activity type
     * - is_active            (bool|nullable)     Default true
     *
     * @return
     * Newly created Activity JSON resource.
     */
    Route::post('/', [ActivityController::class, 'store'])
        ->name('activities.store');


    /**
     * ----------------------------------------------------------------------
     * 3. Show Activity Details
     * ----------------------------------------------------------------------
     *
     * @name   Activity Show
     * @route  GET /api/v1/activities/{activity}
     *
     * @description
     * Retrieves full details of a single activity including sessions.
     *
     * @urlParams
     * - activity (int|required) Activity ID
     *
     * @return
     * Full Activity JSON object.
     */
    Route::get('{activity}', [ActivityController::class, 'show'])
        ->name('activities.show');


    /**
     * ----------------------------------------------------------------------
     * 4. Update Activity
     * ----------------------------------------------------------------------
     *
     * * @name   Activity Update
     * @route  PUT /api/v1/activities/{activity}
     *
     * @description
     * Updates an existing activity and purges cache tags.
     *
     * @bodyParams (UpdateActivityRequest)
     * - program_id           (int|nullable)
     * - profession_id        (int|nullable)
     * - provider_entity_id   (int|nullable)
     * - name                 (string|nullable)
     * - description          (string|nullable)
     * - activity_type        (string|nullable)
     * - is_active            (bool|nullable)
     *
     * @urlParams
     * - activity (int|required)
     *
     * @return
     * Updated Activity JSON resource.
     */
    Route::put('{activity}', [ActivityController::class, 'update'])
        ->name('activities.update');


    /**
     * ----------------------------------------------------------------------
     * 5. Delete Activity
     * ----------------------------------------------------------------------
     *
     * @name   Activity Delete
     * @route  DELETE /api/v1/activities/{activity}
     *
     * @description
     * Deletes an activity record and flushes list/detail caches.
     *
     * @urlParams
     * - activity (int|required)
     *
     * @return
     * Success response message.
     */
    Route::delete('{activity}', [ActivityController::class, 'destroy'])
        ->name('activities.destroy');


    /**
     * ----------------------------------------------------------------------
     * 6. Update Activity Activation State
     * ----------------------------------------------------------------------
     *
     * @name   Activity Update Activation
     * @route  PUT /api/v1/activities/{activity}/updateActivation
     *
     * @description
     * Updates only the activation state (is_active) for an activity.
     * Flushes cache tags to prevent stale activity listings.
     *
     * @urlParams
     * - activity (int|required)
     *
     * @bodyParams
     * - is_active (bool|required)
     *
     * @return
     * Updated Activity JSON resource with new activation state.
     */
    Route::put('{activity}/updateActivation', [ActivityController::class, 'updateActivation'])
        ->name('activities.updateActivation');

});
