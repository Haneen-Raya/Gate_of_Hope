<?php

/**
 * Program Management API Routes - Version 1
 * * This file defines the entry points for the Programs module.
 * It follows the RESTful API design principles and integrates with:
 * - ProgramController: Orchestrates the business logic via ProgramService.
 * - Middleware: Enforces Sanctum authentication and Spatie-based permission checks.
 * - Caching: Leverages tagged caching for optimized data retrieval.
 * * @package Modules\Programs\Routes\V1
 */

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\Api\V1\ProgramController;
use Modules\Programs\Http\Controllers\Api\V1\ProgramPerformanceReportController;

/*
|--------------------------------------------------------------------------
| API V1 - Program Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/programs
| Access: Authenticated Users (Sanctum)
*/

Route::prefix('programs')->group(function () {

    /**
     * Fetch a paginated collection of programs.
     * * @method GET
     * @endpoint /api/v1/programs
     * @access-control Middleware('can:viewAny,Program')
     * @query-parameters
     * - string 'search': Partial match on program name.
     * - string 'status': Filter by ProgramStatus Enum values.
     * - int 'per_page': Pagination limit (default: 15).
     */
    Route::get('/', [ProgramController::class, 'index']);

    /**
     * Create and persist a new program instance.
     * * @method POST
     * @endpoint /api/v1/programs
     * @access-control Middleware('can:create,Program')
     * @request-body StoreProgramRequest (Validated JSON)
     */
    Route::post('/', [ProgramController::class, 'store']);

    /**
     * Retrieve detailed information for a specific program.
     * * @method GET
     * @endpoint /api/v1/programs/{id}
     * @access-control Middleware('can:view,program')
     * @caching Individual Tagged Cache (program_detail_{id})
     */
    Route::get('/{id}', [ProgramController::class, 'show']);

    /**
     * Update an existing program's attributes.
     * * @method PUT
     * @endpoint /api/v1/programs/{program}
     * @access-control Middleware('can:update,program')
     * @logic Validates State Transitions via ProgramStatus Enum.
     */
    Route::put('/{program}', [ProgramController::class, 'update']);

    /**
     * Remove a program from the primary storage.
     * * @method DELETE
     * @endpoint /api/v1/programs/{program}
     * @access-control Middleware('can:delete,program')
     * @auditing Logs 'program.deleted' event upon success.
     */
    Route::delete('/{program}', [ProgramController::class, 'destroy']);

    /**
     * Retrieve detailed information for a specific program.
     * * @method GET
     * @endpoint /api/v1/programs/{id}/reports/performance
     * 
     */
    Route::get('/{id}/reports/performance', [ProgramPerformanceReportController::class, 'show']);
});
