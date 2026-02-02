<?php

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\Api\V1\ActivityAttendanceController;

/*
|--------------------------------------------------------------------------
| Activity Attendance Management Module - API V1
|--------------------------------------------------------------------------
|
| Controller : ActivityAttendanceController
| Model      : ActivityAttendance
| Base Path  : /api/v1/activity-attendances
|
| Activity Attendances track beneficiary participation in scheduled
| program activities. Each record is linked to a specific session,
| recorded by a user, with attendance status and optional notes.
|
|--------------------------------------------------------------------------
*/

Route::prefix('activity-attendances')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Search Activity Attendances
     * ----------------------------------------------------------------------
     *
     * @name   Activity Attendance Index
     * @route  GET /api/v1/activity-attendances
     *
     * @description
     * Returns a paginated list of activity attendance records with dynamic filtering support.
     *
     * @queryParams
     * - activity_session_id  (int|null)     Filter by activity session
     * - beneficiary_id       (int|null)     Filter by beneficiary
     * - recorded_by          (int|null)     Filter by user who recorded attendance
     * - attendance_status    (string|null)  Filter by attendance status
     * - page                 (int)          Pagination page number (default: 1)
     *
     * @features
     * - Custom ActivityAttendanceBuilder Filters
     * - Tagged Caching
     * - Pagination Support
     */
    Route::get('/', [ActivityAttendanceController::class, 'index'])
        ->name('activity-attendances.index');


    /**
     * ----------------------------------------------------------------------
     * 2. Store New Activity Attendance
     * ----------------------------------------------------------------------
     *
     * @name   Activity Attendance Store
     * @route  POST /api/v1/activity-attendances
     *
     * @description
     * Creates a new activity attendance record and flushes related cache tags.
     *
     * @bodyParams (StoreActivityAttendanceRequest)
     * - activity_session_id  (int|required)     The session this attendance belongs to
     * - beneficiary_id       (int|required)     Beneficiary attending the session
     * - recorded_by          (int|required)     User recording this attendance
     * - attendance_status    (string|required)  Attendance status (e.g., present, absent)
     * - notes                (string|nullable) Optional notes
     *
     * @return
     * Newly created Activity Attendance JSON resource.
     */
    Route::post('/', [ActivityAttendanceController::class, 'store'])
        ->name('activity-attendances.store');


    /**
     * ----------------------------------------------------------------------
     * 3. Get Activity Attendance Profile
     * ----------------------------------------------------------------------
     *
     * @name   Activity Attendance Show
     * @route  GET /api/v1/activity-attendances/{activity_attendance}
     *
     * @description
     * Returns full details of a single activity attendance record.
     *
     * @urlParams
     * - activity_attendance  (int)  The ID of the activity attendance
     *
     * @return
     * Activity Attendance JSON resource with session and beneficiary relations.
     */
    Route::get('{activity_attendance}', [ActivityAttendanceController::class, 'show'])
        ->name('activity-attendances.show');


    /**
     * ----------------------------------------------------------------------
     * 4. Full/Partial Update
* ----------------------------------------------------------------------
     *
     * @name   Activity Attendance Update
     * @route  PUT /api/v1/activity-attendances/{activity_attendance}
     *
     * @description
     * Updates an existing activity attendance record and flushes related cache tags.
     *
     * @bodyParams (UpdateActivityAttendanceRequest)
     * - activity_session_id  (int|nullable)     Update session reference
     * - beneficiary_id       (int|nullable)     Update beneficiary
     * - recorded_by          (int|nullable)     Update recorder
     * - attendance_status    (string|nullable)  Update attendance status
     * - notes                (string|nullable)  Update notes
     *
     * @return
     * Updated Activity Attendance JSON resource.
     */
    Route::put('{activity_attendance}', [ActivityAttendanceController::class, 'update'])
        ->name('activity-attendances.update');


    /**
     * ----------------------------------------------------------------------
     * 5. Delete Activity Attendance
     * ----------------------------------------------------------------------
     *
     * @name   Activity Attendance Delete
     * @route  DELETE /api/v1/activity-attendances/{activity_attendance}
     *
     * @description
     * Soft or permanent deletes an activity attendance record and flushes relevant cache tags.
     *
     * @urlParams
     * - activity_attendance  (int)  The ID of the activity attendance
     */
    Route::delete('{activity_attendance}', [ActivityAttendanceController::class, 'destroy'])
        ->name('activity-attendances.destroy');

});
