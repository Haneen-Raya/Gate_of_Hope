<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point
|--------------------------------------------------------------------------
|
| This is the main gate for the API module. Every request must pass through
| the Sanctum authentication and Locale middleware before being routed
| to its specific domain controller.
|
*/

Route::middleware(['auth:sanctum','set_locale_lang'])->prefix('v1')->group(function () {

    /** * Professions Domain:
     * Handles endpoints related to professional categories and certifications.
     */
    require __DIR__ . '/V1/professions.php';

    /** * Specialists Domain:
     * Manages routes for psychologist and social worker profiles.
     */
    require __DIR__ . '/v1/specialists.php';

    /** * Trainers Domain:
     * Routes for managing educational and activity trainer entities.
     */
    require __DIR__ . '/v1/trainers.php';
});
