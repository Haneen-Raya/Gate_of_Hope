<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\CaseEventController;

/*
|--------------------------------------------------------------------------
| Case Timeline & Event Audit - API V1
|--------------------------------------------------------------------------
| Controller: CaseEventController
| Model: CaseEvent
| Base Path: /api/v1/case-events
| Description: Primary API gateway for retrieving the beneficiary 
| historical timeline and system-wide audit trails.
|--------------------------------------------------------------------------
*/

Route::prefix('case-events')->group(function () {

    /**
     * @name 1. List & Filter Case Timeline
     * @path GET /api/v1/case-events
     * * @query_params:
     * - @param beneficiary_id (int): Filter timeline by a specific beneficiary.
     * - @param beneficiary_case_id (int): Filter events for a particular case file.
     * - @param event_tag (string): Filter by event type using Enum values (e.g., case.opened).
     * - @param actor_id (int): Filter by the specialist/user who triggered the event.
     * - @param subject_type (string): Filter by the source entity (e.g., CaseSession).
     * - @param date_from/date_to (date): Temporal range filtering for occurred_at.
     * * @features: 
     * - Tagged Caching: Uses 'case_events' global tag.
     * - Performance: MD5 Key Generation with ksort normalization.
     * - Logic: Powered by CaseEventBuilder for deep Eloquent filtering.
     */
    Route::get('/', [CaseEventController::class, 'index'])
        ->name('case-events.index');

    /**
     * @name 2. Get Specific Event Metadata
     * @path GET /api/v1/case-events/{id}
     * * @url_params:
     * - id (int): Primary identifier of the event record.
     * * @features: 
     * - Dual-Layer Caching: Purgeable via 'case_events' OR 'case_event_{id}'.
     * - Data Integrity: Returns deep-payload snapshots of the model state at that moment.
     * - Authorization: Managed via CaseEventPolicy.
     */
    Route::get('{id}', [CaseEventController::class, 'show'])
        ->name('case-events.show');

});