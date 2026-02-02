<?php

/**
 * API Routing Entry Point - Programs Module
 *
 * This file serves as the primary router for the Programs module.
 * It encapsulates all versioned API routes under a secured umbrella,
 * ensuring global standards for authentication and versioning are applied.
 *
 * Key Responsibilities:
 * - Versioning: Enforces the 'v1' prefix for all internal routes.
 * - Security: Applies 'auth:sanctum' middleware to ensure all endpoints are protected.
 * - Modularization: Loads specific sub-route files to maintain a clean and scalable structure.
 *
 * @package Modules\Programs\Routes
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Main API Route Group
 * * Applies global middleware for authentication and sets the API version prefix.
 * Includes sub-route files for better organization and separation of concerns.
 * * Middleware: auth:sanctum (Ensures user is authenticated)
 * Prefix: v1 (Version control)
 */
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    /**
     * Program Management Routes (v1)
     * * Handles all core program logic including CRUD, state transitions, and caching.
     * Separated into a dedicated file for maintainability.
     * * @see Modules/Programs/Routes/v1/program.php
     */
    require __DIR__ . '/v1/program.php';

    /**
     * Resource Allocation Routes (v1)
     * * Manages educational materials, logistics, and equipment assigned to programs.
     * * @see Modules/Programs/Routes/v1/resource.php
     */
    require __DIR__ . '/v1/resource.php';

    require __DIR__ . '/v1/activity-sessions.php';
    
    // Register activities routes
    require __DIR__ . '/V1/activities.php';

    // Register activity attendances routes
    require __DIR__ . '/V1/activity_attendances.php';
});
