<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\Api\V1\BeneficiaryController;

/*
|--------------------------------------------------------------------------
| Beneficiary Management Module - API V1
|--------------------------------------------------------------------------
| Controller: BeneficiaryController
| Model: Beneficiary
| Base Path: /api/v1/beneficiariess
|--------------------------------------------------------------------------
*/

Route::prefix('beneficiaries')->group(function () {

    /**
     * @name 1. List & Search Beneficiaries
     * @path GET /api/v1/beneficiaries
     * 
     * @query_params:
     * - @param search (string): Search by system_code OR plain national_id.
     * - @param governorate (enum): Filter by region (see Governorate Enum).
     * - @param gender (enum): Filter by gender (male/female).
     * - @param disability_type (enum): Filter by disability classification.
     * - @param residence_type (enum): Filter by housing status.
     * - @param is_verified (bool): Filter by verification status (0/1).
     * - @param is_displaced (bool): Filter by displacement status (0/1).
     * - @param birth_date_from (date): Filter by DOB (YYYY-MM-DD) - start range.
     * - @param birth_date_to (date): Filter by DOB (YYYY-MM-DD) - end range.
     * - @param page (int): Pagination page number (default: 1).
     * 
     * @features: Tagged Caching, Dynamic Scopes, Custom Builder.
     */
    Route::get('/', [BeneficiaryController::class, 'index'])
        ->name('beneficiaries.index');

    /**
     * @name 2. Store New Beneficiary
     * @path POST /api/v1/beneficiaries
     * 
     * @body_payload (BeneficiaryRequest):
     * - user_id (int/required): Owner of the record (stored automaticly).
     * - national_id (string/required/unique): Encrypted at rest.
     * - date_of_birth (date/required): Used for age-based reporting.
     * - identity_file (file/required): Uploaded to private 'identities' collection.
     * - [other fields]: governorate, gender, residence_type, etc.
     * 
     * * @description registers beneficiary, hashes ID for lookup, and generates serial code.
     */
    Route::post('/', [BeneficiaryController::class, 'store'])
        ->name('beneficiaries.store');

    /**
     * @name 3. Get Beneficiary Profile
     * @path GET /api/v1/beneficiaries/{beneficiary}
     * 
     * @url_params:
     * - beneficiary (int): The ID of the beneficiary.
     * 
     * @return Full JSON object.
     */
    Route::get('{beneficiary}', [BeneficiaryController::class, 'show'])
        ->name('beneficiaries.show');

    /**
     * @name 4. Full/Partial Update
     * @path PUT /api/v1/beneficiaries/{beneficiary}
     * 
     * @description Updates the beneficiary record and purges all related cache tags
     * to prevent stale data in the list view (Ripple Effect Invalidation).
     */
    Route::put('{beneficiary}', [BeneficiaryController::class, 'update'])
        ->name('beneficiaries.update');

    /**
     * @name 5. Soft Delete Beneficiary
     * @path DELETE /api/v1/beneficiaries/{beneficiary}
     * 
     * @description Utilizes Eloquent SoftDeletes. The record will be excluded from
     * standard queries but remains in the database for audit and archiving.
     */
    Route::delete('{beneficiary}', [BeneficiaryController::class, 'destroy'])
        ->name('beneficiaries.destroy');

    /**
     * @name 6. Secure Identity Stream
     * @path GET /api/v1/beneficiaries/{beneficiary}/identity
     * 
     * @security:
     * - middleware: 'signed' (Requires a temporary URL with valid signature).
     * - access: Streams file from 'local' private disk (non-public).
     * 
     * @description Serves the JPEG/PDF identity file directly to the browser.
     */
    Route::get('{beneficiary}/identity', [BeneficiaryController::class, 'showIdentity'])
        ->name('beneficiaries.identity')
        ->middleware('signed');
});
