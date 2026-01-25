<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\CasePlanGoalController;

/*
|--------------------------------------------------------------------------
| Case Plan Goal Management - API V1
|--------------------------------------------------------------------------
| Controller: CasePlanGoalController
| Model: CasePlanGoal
| Base Path: /api/v1/case-plan-goals
|--------------------------------------------------------------------------
*/

Route::prefix('case-plan-goals')->group(function () {

    /**
     * @name 1. List & Filter Plan Goals
     * @path GET /api/v1/case-plan-goals
     * * @query_params:
     * - @param plan_id (int): Mandatory for Coordinators, Optional for Admins.
     * - @param status (string): Filter by (pending, in-progress, achieved, cancelled).
     * - @param only_achieved (bool): Quick filter for completed objectives.
     * - @param only_overdue (bool): Targets goals past their target_date.
     * - @param target_from/to (date): Chronological range filtering.
     * * @features: Tagged Caching (Global/Resource), Parameter Normalization (ksort).
     */
    Route::get('/', [CasePlanGoalController::class, 'index'])
        ->name('case-plan-goals.index');

    /**
     * @name 2. Store New Case Plan Goal
     * @path POST /api/v1/case-plan-goals
     * * @body_payload (StoreCasePlanGoalRequest):
     * - plan_id (int/required): Parent support plan reference.
     * - goal_description (string/required): Narrative of the objective.
     * - target_date (date/required): Must be within parent plan duration.
     * - status (string/required): Initial lifecycle state.
     * * @description Enforces temporal integrity via Custom Validation Rules.
     */
    Route::post('/', [CasePlanGoalController::class, 'store'])
        ->name('case-plan-goals.store');

    /**
     * @name 3. Get Specific Goal Details
     * @path GET /api/v1/case-plan-goals/{id}
     * * @url_params:
     * - id (int): Primary identifier of the goal.
     * * @return Fresh or Cached goal instance with associated audit logs.
     */
    Route::get('{id}', [CasePlanGoalController::class, 'show'])
        ->name('case-plan-goals.show');

    /**
     * @name 4. Update Goal Milestone
     * @path PUT /api/v1/case-plan-goals/{case_plan_goal}
     * * @description Handles state transitions. 
     * - Note: Automatically sets 'achieved_at' if status is changed to 'achieved'.
     * - Purges: Global tag AND specific resource tag.
     */
    Route::put('{case_plan_goal}', [CasePlanGoalController::class, 'update'])
        ->name('case-plan-goals.update');

    /**
     * @name 5. Terminate Plan Goal
     * @path DELETE /api/v1/case-plan-goals/{case_plan_goal}
     * * @description Removes the goal and triggers a 'Ripple Effect' cache invalidation 
     * to ensure lists reflect the deletion immediately.
     */
    Route::delete('{case_plan_goal}', [CasePlanGoalController::class, 'destroy'])
        ->name('case-plan-goals.destroy');
});