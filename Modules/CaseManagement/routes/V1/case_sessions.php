<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\CaseSessionController;

/*
|--------------------------------------------------------------------------
| Case Management Module - API V1
|--------------------------------------------------------------------------
| Controller: CaseSessionController
| Model: CaseSession
| Base Path: /api/v1/case-management
|--------------------------------------------------------------------------
*/

Route::prefix('case-management')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List Case Sessions (Paginated)
     * ----------------------------------------------------------------------
     * GET /api/v1/case-management/cases/{caseId}/sessions
     *
     * @name case-sessions.index
     * @url_params:
     *  - caseId (int): Beneficiary Case ID
     * @query_params:
     *  - page (int): Page number (default 1)
     *  - per_page (int): Items per page (default 10)
     * @features:
     *  - Paginated list of sessions
     *  - Custom query builder
     *  - Cached per case
     */
    Route::get('cases/{caseId}/sessions', [CaseSessionController::class, 'index'])
        ->name('case-sessions.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Case Session
     * ----------------------------------------------------------------------
     * POST /api/v1/case-management/cases/{beneficiaryCase}/sessions
     *
     * @name case-sessions.store
     * @body_payload: StoreCaseSessionRequest
     *  - session_type (enum, required)
     *  - session_date (date, required)
     *  - duration_minutes (int, nullable)
     *  - notes (string, nullable)
     *  - recommendations (string, nullable)
     *  - conducted_by (int, required)
     * @description:
     *  Creates a new session and clears related case cache.
     */
    Route::post('cases/{beneficiaryCase}/sessions', [CaseSessionController::class, 'store'])
        ->name('case-sessions.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Single Case Session
     * ----------------------------------------------------------------------
     * GET /api/v1/case-management/sessions/{caseSession}
     *
     * @name case-sessions.show
     * @url_params:
     *  - caseSession (int): CaseSession ID
     * @features:
     *  - Route model binding
     *  - Cached by session ID
     */
    Route::get('sessions/{caseSession}', [CaseSessionController::class, 'show'])
        ->whereNumber('caseSession')
        ->name('case-sessions.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Case Session (Partial Update)
     * ----------------------------------------------------------------------
     * PUT /api/v1/case-management/sessions/{caseSession}
     *
     * @name case-sessions.update
     * @body_payload: UpdateCaseSessionRequest
     *  - session_type (enum, sometimes)
     *  - session_date (date, sometimes)
     *  - duration_minutes (int, nullable)
     *  - notes (string, nullable)
     *  - recommendations (string, nullable)
     * @description:
     *  Updates session and clears case & session cache.
     */
    Route::put('sessions/{caseSession}', [CaseSessionController::class, 'update'])
        ->whereNumber('caseSession')
        ->name('case-sessions.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Case Session
     * ----------------------------------------------------------------------
     * DELETE /api/v1/case-management/sessions/{caseSession}
     *
     * @name case-sessions.destroy
     * @description:
     *  Permanently deletes session and clears cache.
     */
    Route::delete('sessions/{caseSession}', [CaseSessionController::class, 'destroy'])
        ->whereNumber('caseSession')
        ->name('case-sessions.destroy');

    /**
     * ----------------------------------------------------------------------
     * 6. Get Sessions by Specialist
     * ----------------------------------------------------------------------
     * GET /api/v1/case-management/specialists/{specialistId}/sessions
     *
     * @name case-sessions.by-specialist
     * @url_params:
     *  - specialistId (int): Specialist ID
     * @query_params:
     *  - page (int)
     *  - per_page (int)
     * @features:
     *  - Paginated sessions per specialist
     *  - Cached by specialist
     */
    Route::get('specialists/{specialistId}/sessions', [CaseSessionController::class, 'bySpecialist'])
        ->whereNumber('specialistId')
        ->name('case-sessions.by-specialist');

    /**
     * ----------------------------------------------------------------------
     * 7. Count Sessions for a Case
     * ----------------------------------------------------------------------
     * GET /api/v1/case-management/cases/{caseId}/sessions/count
     *
     * @name case-sessions.count
     * @return { count: int }
     */
    Route::get('cases/{caseId}/sessions/count', [CaseSessionController::class, 'count'])
        ->whereNumber('caseId')
        ->name('case-sessions.count');

    /**
     * ----------------------------------------------------------------------
     * 8. Get Available Session Types (Enum)
     * ----------------------------------------------------------------------
     * GET /api/v1/case-management/sessions/types
     *
     * @name case-sessions.types
     * @description:
     *  Returns all SessionType enum values for frontend dropdowns.
     */
    Route::get('sessions/types', [CaseSessionController::class, 'sessionTypes'])
        ->name('case-sessions.types');

    /**
     * ----------------------------------------------------------------------
     * 9. Get ALL sessions for a case (No Pagination)
     * ----------------------------------------------------------------------
     * GET /api/v1/case-management/cases/{caseId}/sessions/all
     */
    Route::get('cases/{caseId}/sessions/all', [CaseSessionController::class, 'allForCase'])
        ->whereNumber('caseId')
        ->name('case-sessions.all-for-case');

    /**
     * ----------------------------------------------------------------------
     * 10. Get Sessions for a Case Between Dates
     * ----------------------------------------------------------------------
     * GET /api/v1/case-management/cases/{caseId}/sessions/between/{from}/{to}
     *
     * @url_params:
     *  - caseId (int): Beneficiary Case ID
     *  - from (string): Start date YYYY-MM-DD
     *  - to (string): End date YYYY-MM-DD
     */
    Route::get('cases/{caseId}/sessions/between/{from}/{to}', [CaseSessionController::class, 'forCaseBetweenDates'])
        ->name('case-sessions.between-dates');

});
