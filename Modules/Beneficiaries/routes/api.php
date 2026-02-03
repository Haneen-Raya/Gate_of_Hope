<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point - Beneficiary Module
|--------------------------------------------------------------------------
|
| This segment handles the demographic and socio-economic profile of
| beneficiaries. It utilizes a modular approach to handle various
| classification lookups.
|
*/

Route::middleware(['auth:sanctum', 'set_locale_lang'])->prefix('v1')->group(function () {

    /** * Primary Records:
     * Main CRUD for beneficiary personal and contact data.
     */
    require __DIR__ . '/V1/beneficiaries.php';

    /** * Educational Profiles:
     * Classification for academic achievements (e.g., Primary, Secondary, University).
     */
    require __DIR__ . '/V1/education_levels.php';

    /** * Labor Market Status:
     * Tracking employment, unemployment, or vocational training status.
     */
    require __DIR__ . '/V1/employment_statuses.php';

    /** * Living Conditions:
     * Defining housing situations (e.g., Owned, Rented, Refugee Camp).
     */
    require __DIR__ . '/V1/housing_types.php';

    /** * Social Context:
     * Historical and social background classification for case analysis.
     */
    require __DIR__ . '/V1/social_backgrounds.php';

});
