<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\Api\V1\SocialBackgroundController;

/*
|--------------------------------------------------------------------------
| Social Background Management Module - API V1
|--------------------------------------------------------------------------
| Controller: SocialBackgroundController
| Model: SocialBackground
| Base Path: /api/v1/social-backgrounds
| This module manages the social background data
| associated with beneficiaries
|--------------------------------------------------------------------------
*/

Route::prefix('social-backgrounds')->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1. List & Filter Social Backgrounds
     * ----------------------------------------------------------------------
     *
     * @name   Social Background Index
     * @route  GET /api/v1/social-backgrounds
     *
     * @description
     * Returns a paginated list of social background records.
     * Supports dynamic filtering using SocialBackgroundsBuilder.
     *
     * @queryParams
     * - beneficiary_id (int|null)        Filter by beneficiary.
     * - education_level_id (int|null)    Filter by education level.
     * - employment_status_id (int|null)  Filter by employment status.
     * - housing_type_id (int|null)       Filter by housing type.
     *
     * - housing_tenure (string|null)     Filter by housing tenure.
     * - income_level (string|null)       Filter by income level.
     * - living_standard (string|null)    Filter by living standard.
     * - family_stability (string|null)   Filter by family stability.
     *
     * - min_family_size (int|null)       Minimum family size.
     * - max_family_size (int|null)       Maximum family size.
     *
     * - page (int)                       Pagination page number.
     *
     * @features
     * - Custom Builder Filtering
     * - Tagged Caching Support
     */
    Route::get('/', [SocialBackgroundController::class, 'index'])
        ->name('social-backgrounds.index');

    /**
     * ----------------------------------------------------------------------
     * 2. Store New Social Background
     * ----------------------------------------------------------------------
     *
     * @name   Social Background Store
     * @route  POST /api/v1/social-backgrounds
     *
     * @description
     * Creates a new social background record linked to a beneficiary.
     * Automatically invalidates cached lists (Ripple Effect).
     *
     * @bodyParams (StoreSocialBackgroundRequest)
     * - beneficiary_id (int|required)        Beneficiary reference.
     * - education_level_id (int|required)    Education level reference.
     * - employment_status_id (int|required)  Employment status reference.
     * - housing_type_id (int|required)       Housing type reference.
     *
     * - housing_tenure (string|required)     Enum value (e.g. owned, rented).
     * - income_level (string|required)       Enum value (low, medium, high).
     * - living_standard (string|required)    Enum value.
     *
     * - family_size (int|nullable)           Number of family members.
     * - family_stability (string|required)   Enum value.
     *
     * @return
     * Newly created SocialBackground JSON resource.
     */
    Route::post('/', [SocialBackgroundController::class, 'store'])
        ->name('social-backgrounds.store');

    /**
     * ----------------------------------------------------------------------
     * 3. Show Social Background Details
     * ----------------------------------------------------------------------
     *
     * @name   Social Background Show
     * @route  GET /api/v1/social-backgrounds/{social_background}
     *
     * @description
     * Retrieves a single social background record by its ID.
     *
     * @urlParams
     * - social_background (int|required)  Social background ID.
     *
     * @return
     * Full SocialBackground JSON object.
     */
    Route::get('{social_background}', [SocialBackgroundController::class, 'show'])
        ->name('social-backgrounds.show');

    /**
     * ----------------------------------------------------------------------
     * 4. Update Social Background
     * ----------------------------------------------------------------------
     *
     * @name   Social Background Update
     * @route  PUT /api/v1/social-backgrounds/{social_background}
     *
     * @description
     * Updates an existing social background record.
     * Flushes all related cache tags to prevent stale data.
     *
     * @urlParams
     * - social_background (int|required)
     *
     * @bodyParams (UpdateSocialBackgroundRequest)
     * Accepts the same fields as the store endpoint (partial or full update).
     *
     * @return
     * Updated SocialBackground JSON resource.
     */
    Route::put('{social_background}', [SocialBackgroundController::class, 'update'])
        ->name('social-backgrounds.update');

    /**
     * ----------------------------------------------------------------------
     * 5. Delete Social Background
     * ----------------------------------------------------------------------
     *
     * @name   Social Background Delete
     * @route  DELETE /api/v1/social-backgrounds/{social_background}
     *
     * @description
     * Soft deletes the social background record.
     * Triggers cache invalidation for both the resource and list caches.
     *
     * @urlParams
     * - social_background (int|required)
     *
     * @return
     * Success response message.
     */
    Route::delete('{social_background}', [SocialBackgroundController::class, 'destroy'])
        ->name('social-backgrounds.destroy');
});
