<?php

use Illuminate\Support\Facades\Route;
use Modules\Assessments\Http\Controllers\Api\V1\PriorityRuleController;

/*
|--------------------------------------------------------------------------
| Priority Rules Management - API V1 Routes
|--------------------------------------------------------------------------
| Purpose: Manages automated scoring logic to assign Case Priority.
| Features: Range-based validation (min/max score), status toggling.
| Caching: Integrated with AutoFlushCache via PriorityRules Model.
|--------------------------------------------------------------------------
*/

Route::prefix('v1/priority-rules')->group(function () {

    /**
     * @name 1. List All Priority Rules
     * @path GET /api/v1/priority-rules
     * @features:
     * - Tagged Caching (priority_rules_global)
     * - Eager loads 'issueType' relationship.
     */
    Route::get('/', [PriorityRuleController::class, 'index']);

    /**
     * @name 2. Store New Priority Rule
     * @path POST /api/v1/priority-rules
     * @features:
     * - Validates non-overlapping score ranges.
     * - Automatic cache invalidation on success.
     */
    Route::post('/', [PriorityRuleController::class, 'store']);

    /**
     * @name 3. Get Rule Details
     * @path GET /api/v1/priority-rules/{id}
     * @features:
     * - Specific cache tag (priority_rule_{id})
     */
    Route::get('/{id}', [PriorityRuleController::class, 'show']);

    /**
     * @name 4. Update Priority Rule
     * @path PUT /api/v1/priority-rules/{id}
     * @features:
     * - Partial updates allowed.
     * - Logical check: max_score must be > min_score.
     */
    Route::put('/{id}', [PriorityRuleController::class, 'update']);

    /**
     * @name 5. Delete Priority Rule
     * @path DELETE /api/v1/priority-rules/{id}
     * @features: Immediate purge of associated cache tags.
     */
    Route::delete('/{id}', [PriorityRuleController::class, 'destroy']);
});
