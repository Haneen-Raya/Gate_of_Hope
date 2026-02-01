<?php

use Illuminate\Support\Facades\Route;
use Modules\Programs\Http\Controllers\Api\V1\ProgramController;

/*
|--------------------------------------------------------------------------
| Program Management - API V1 Routes
|--------------------------------------------------------------------------
| File Path: Modules/Programs/Routes/v1/program.php
| Purpose: Handles all program-related CRUD with Caching & Policies.
|--------------------------------------------------------------------------
*/

Route::prefix('programs')->group(function () {

    /**
     * @name List Programs
     * @path GET /api/v1/programs
     * @query_params: search, status, per_page, page.
     */
    Route::get('/', [ProgramController::class, 'index']);

    /**
     * @name Store Program
     * @path POST /api/v1/programs
     */
    Route::post('/', [ProgramController::class, 'store']);

    /**
     * @name Show Program Details
     * @path GET /api/v1/programs/{id}
     * @features Audit Logging, Individual Tagged Cache.
     */
    Route::get('/{id}', [ProgramController::class, 'show']);

    /**
     * @name Update Program
     * @path PUT /api/v1/programs/{program}
     */
    Route::put('/{program}', [ProgramController::class, 'update']);

    /**
     * @name Delete Program
     * @path DELETE /api/v1/programs/{program}
     */
    Route::delete('/{program}', [ProgramController::class, 'destroy']);
});
