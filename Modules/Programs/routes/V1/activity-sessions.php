<?php

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\Api\V1\ActivitySessionController;

/**
 * --------------------------------------------------------------------------
 * Programs Module - API V1 Routes
 * --------------------------------------------------------------------------
 *
 * Resource: Activity Sessions
 * Controller: ActivitySessionController
 * Model: ActivitySession
 *
 * Base URI: /api/v1/programs
 * Middleware:
 * - auth:sanctum
 *
 * This file defines all API routes related to activity sessions,
 * including CRUD operations, status transitions, spatial queries,
 * and upcoming session listings.
 * --------------------------------------------------------------------------
 */

Route::middleware(['auth:sanctum'])->prefix('programs')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List Activity Sessions (Paginated)
     * ----------------------------------------------------------------------
     *
     * @route   GET /api/v1/programs/activity-sessions
     * @name    activity_sessions.index
     *
     * Query Parameters:
     * - page (int, optional)
     * - per_page (int, optional)
     * - trainer_id (int, optional): Filter by trainer
     * - activity_id (int, optional): Filter by activity
     * - status (string, optional): scheduled | ongoing | completed | cancelled
     * - session_date (date, optional)
     *
     * Features:
     * - Paginated listing
     * - Query scopes & custom builder
     * - Cached results
     */
    Route::get('activity-sessions', [ActivitySessionController::class, 'index'])
        ->name('activity_sessions.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Create Activity Session
     * ----------------------------------------------------------------------
     *
     * @route   POST /api/v1/programs/activity-sessions
     * @name    activity_sessions.store
     *
     * Request Body (StoreActivitySessionRequest):
     * - activity_id (int, required)
     * - trainer_id (int, required)
     * - session_date (date, required)
     * - start_time (H:i, required)
     * - end_time (H:i, required)
     * - capacity (int, required)
     * - location (object, required): { lat, lng }
     * - status (enum, required)
     * - session_notes (string, optional)
     *
     * Description:
     * Creates a new activity session and clears all related cache entries.
     */
    Route::post('activity-sessions', [ActivitySessionController::class, 'store'])
        ->name('activity_sessions.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Activity Session
     * ----------------------------------------------------------------------
     *
     * @route   GET /api/v1/programs/activity-sessions/{activity_session}
     * @name    activity_sessions.show
     *
     * URL Parameters:
     * - activity_session (int): Activity Session ID
     *
     * Features:
     * - Route Model Binding
     */
    Route::get('activity-sessions/{activity_session}', [ActivitySessionController::class, 'show'])
        ->whereNumber('activity_session')
        ->name('activity_sessions.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Activity Session
     * ----------------------------------------------------------------------
     *
     * @route   PUT /api/v1/programs/activity-sessions/{activity_session}
     * @name    activity_sessions.update
     *
     * Request Body (UpdateActivitySessionRequest):
     * - activity_id (int, optional)
     * - trainer_id (int, optional)
     * - session_date (date, optional)
     * - start_time (H:i, optional)
     * - end_time (H:i, optional)
     * - capacity (int, optional)
     * - location (object, optional)
     * - status (enum, optional)
     * - session_notes (string, optional)
     *
     * Description:
     * Updates the activity session and clears related cache entries.
     */
    Route::put('activity-sessions/{activity_session}', [ActivitySessionController::class, 'update'])
        ->whereNumber('activity_session')
        ->name('activity_sessions.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Activity Session
     * ----------------------------------------------------------------------
     *
     * @route   DELETE /api/v1/programs/activity-sessions/{activity_session}
     * @name    activity_sessions.destroy
     *
     * Description:
     * Deletes the activity session and clears related cache entries.
     */
    Route::delete('activity-sessions/{activity_session}', [ActivitySessionController::class, 'destroy'])
        ->whereNumber('activity_session')
        ->name('activity_sessions.destroy');

    /**
     * ----------------------------------------------------------------------
     * 6. Mark Activity Session as Completed
     * ----------------------------------------------------------------------
     *
     * @route   POST /api/v1/programs/activity-sessions/{activity_session}/complete
     * @name    activity_sessions.complete
     *
     * Description:
     * Marks an ongoing activity session as completed.
     */
    Route::post('activity-sessions/{session}/complete', [ActivitySessionController::class, 'complete'])
        ->whereNumber('session')
        ->name('activity_sessions.complete');

    /**
     * ----------------------------------------------------------------------
     * 7. Cancel Activity Session
     * ----------------------------------------------------------------------
     *
     * @route   POST /api/v1/programs/activity-sessions/{activity_session}/cancel
     * @name    activity_sessions.cancel
     *
     * Description:
     * Cancels an activity session if it is not locked.
     */
    Route::post('activity-sessions/{activity_session}/cancel', [ActivitySessionController::class, 'cancel'])
        ->whereNumber('activity_session')
        ->name('activity_sessions.cancel');

    /**
     * ----------------------------------------------------------------------
     * 8. Nearby Activity Sessions (Location-Based Search)
     * ----------------------------------------------------------------------
     *
     * @route   GET /api/v1/programs/activity-sessions/nearby
     * @name    activity_sessions.nearby
     *
     * Query Parameters:
     * - lat (float, required): Latitude
     * - lng (float, required): Longitude
     * - radius (int, optional): Search radius in meters
     * - activity_id (int, optional): Filter by activity
     *
     * Features:
     * - Spatial (POINT) distance query
     * - Radius-based filtering
     */
    Route::get('activity-sessions/nearby', [ActivitySessionController::class, 'nearby'])
        ->name('activity_sessions.nearby');

    /**
     * ----------------------------------------------------------------------
     * 9. Upcoming Sessions for Trainer
     * ----------------------------------------------------------------------
     *
     * @route   GET /api/v1/programs/upcoming/trainer/{trainer}
     * @name    activity_sessions.upcoming.trainer
     *
     * URL Parameters:
     * - trainer (int): Trainer ID
     */
    Route::get('upcoming/trainer/{trainer}', [ActivitySessionController::class, 'upcomingForTrainer'])
        ->whereNumber('trainer')
        ->name('activity_sessions.upcoming.trainer');

    /**
     * ----------------------------------------------------------------------
     * 10. Upcoming Sessions for Activity
     * ----------------------------------------------------------------------
     *
     * @route   GET /api/v1/programs/upcoming/activity/{activity}
     * @name    activity_sessions.upcoming.activity
     *
     * URL Parameters:
     * - activity (int): Activity ID
     */
    Route::get('upcoming/activity/{activity}', [ActivitySessionController::class, 'upcomingForActivity'])
        ->whereNumber('activity')
        ->name('activity_sessions.upcoming.activity');

});
