<?php

/**
 * API Routing Entry Point - Programs Module
 * * This file serves as the primary router for the Programs module.
 * It encapsulates all versioned API routes under a secured umbrella,
 * ensuring global standards for authentication and versioning are applied.
 * * Key Responsibilities:
 * - Versioning: Enforces the 'v1' prefix for all internal routes.
 * - Security: Applies 'auth:sanctum' middleware to ensure all endpoints are protected.
 * - Modularization: Loads specific sub-route files to maintain a clean and scalable structure.
 * * @package Modules\Programs\Routes
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    /**
     * Program Management Routes (v1)
     * Includes CRUD operations, state transitions, and caching logic.
     * @see Modules/Programs/Routes/v1/program.php
     */
    require __DIR__ . '/v1/program.php';

});
