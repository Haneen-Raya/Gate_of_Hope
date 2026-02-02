<?php

/**
 * Program Resources Sub-Routing
 * * This file defines the specific API endpoints for managing program resources.
 * It is loaded within the main API routing entry point under the 'v1' prefix
 * and 'auth:sanctum' middleware.
 *
 * @package Modules\Programs\Routes\V1
 */

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\Api\V1\ProgramResourceController;

/*
|--------------------------------------------------------------------------
| Program Resource Routes
|--------------------------------------------------------------------------
|
| These routes handle the CRUD operations for resources such as educational
| materials, logistics, and equipment assigned to programs.
|
*/

/**
 * Route Group: Program Resources
 * Prefix: /program-resources
 */
Route::prefix('program-resources')->group(function () {

    /**
     * GET /api/v1/program-resources
     * List all program resources with optional filtering.
     */
    Route::get('/', [ProgramResourceController::class, 'index']);

    /**
     * POST /api/v1/program-resources
     * Create/Allocate a new resource to a program.
     * Includes budget validation logic.
     */
    Route::post('/', [ProgramResourceController::class, 'store']);

    /**
     * GET /api/v1/program-resources/{id}
     * Retrieve detailed information for a specific resource.
     */
    Route::get('/{id}', [ProgramResourceController::class, 'show']);

    /**
     * PUT /api/v1/program-resources/{resource}
     * Update an existing resource's data.
     * Re-validates program budget on quantity or cost changes.
     */
    Route::put('/{resource}', [ProgramResourceController::class, 'update']);

    /**
     * DELETE /api/v1/program-resources/{resource}
     * Remove a resource from the system.
     */
    Route::delete('/{resource}', [ProgramResourceController::class, 'destroy']);
});
