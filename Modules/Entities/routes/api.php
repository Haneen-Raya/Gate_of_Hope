<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\Api\V1\EntitiyController;

/**
 * API V1 Modules Routes
 * * This group handles the external relations of the programs, including
 * who funds them (Donors) and who executes them (Entities).
 */

Route::middleware(['auth:sanctum', 'set_locale_lang'])->prefix('v1')->group(function () {

    /** * Donor Reports Domain:
     * Logic for generating and retrieving progress/financial reports for donors.
     */
    require __DIR__ . '/V1/donor-reports.php';

    /** * Program Fundings Domain:
     * Manages the budget allocations, grants, and funding cycles for specific programs.
     */
    require __DIR__ . '/V1/program_fundings.php';

    /** * Entities Domain:
     * Handles the CRUD operations for external partners, service providers, and NGOs.
     */
    require __DIR__ . '/V1/entities.php';
});
