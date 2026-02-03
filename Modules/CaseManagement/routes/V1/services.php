<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\ServiceController;

/*
|--------------------------------------------------------------------------
| Service Management Module - API V1
|--------------------------------------------------------------------------
|
| Controller : ServiceController
| Model      : Service
| Base Path  : /api/v1/services
|
| Services represent structured interventions that can be delivered
| to beneficiaries, with attributes such as direction, cost, and issue category.
| They can be linked to cases, referrals, and issue categories.
|
|--------------------------------------------------------------------------
*/

Route::prefix('services')->group(function () {

    /*
     * ----------------------------------------------------------------------
     * 1. List & Search Services
     * ----------------------------------------------------------------------
     *
     * @name   Service Index
     * @route  GET /api/v1/services
     *
     * @description
     * Returns a paginated list of services with dynamic filtering support.
     *
     * @queryParams
     * - is_active          (bool|null)    Filter by activation status (0/1)
     * - direction          (string|null)  Filter by service direction (internal/external)
     * - issue_category_id  (int|null)     Filter by related issue category
     * - min_cost           (float|null)   Minimum unit cost filter
     * - max_cost           (float|null)   Maximum unit cost filter
     * - name               (string|null)  Search by service name (LIKE)
     * - page               (int)          Pagination page number (default: 1)
     *
     * @features
     * - Custom ServiceBuilder Filters
     * - Tagged Caching
     * - Pagination Support
     */
    Route::get('/', [ServiceController::class, 'index'])
        ->name('services.index');


    /*
     * ----------------------------------------------------------------------
     * 2. Store New Service
     * ----------------------------------------------------------------------
     *
     * @name   Service Store
     * @route  POST /api/v1/services
     *
     * @description
     * Creates a new service and flushes related cache tags.
     *
     * @bodyParams (StoreServiceRequest)
     * - issue_category_id  (int|required)     Related issue category
     * - name               (string|required)  Service name
     * - description        (string|nullable)  Service description
     * - direction          (string|required)  Service direction (internal/external)
     * - unit_cost          (float|required)   Unit cost for this service
     * - is_active          (bool|nullable)    Activation state (default true)
     *
     * @return
     * Newly created Service JSON resource.
     */
    Route::post('/', [ServiceController::class, 'store'])
        ->name('services.store');


    /*
     * ----------------------------------------------------------------------
     * 3. Get Service Profile
     * ----------------------------------------------------------------------
     *
     * @name   Service Show
     * @route  GET /api/v1/services/{service}
     *
     * @description
     * Returns full details of a single service record.
     *
     * @urlParams
     * - service  (int)  The ID of the service
     *
     * @return
     * Service JSON resource including issue category relations.
     */
    Route::get('{service}', [ServiceController::class, 'show'])
        ->name('services.show');


    /*
     * ----------------------------------------------------------------------
     * 4. Full/Partial Update
     * ----------------------------------------------------------------------
     *
     * @name   Service Update
     * @route  PUT /api/v1/services/{service}
     *
     * @description
     * Updates an existing service record and flushes related cache tags.
     *
     * @bodyParams (UpdateServiceRequest)
     * - issue_category_id  (int|nullable)    Update issue category
     * - name               (string|nullable) Update service name
     * - description        (string|nullable) Update description
     * - direction          (string|nullable) Update service direction
     * - unit_cost          (float|nullable)  Update unit cost
     * - is_active          (bool|nullable)   Update activation state
     *
     * @return
     * Updated Service JSON resource.
     */
    Route::put('{service}', [ServiceController::class, 'update'])
        ->name('services.update');


    /*
     * ----------------------------------------------------------------------
     * 5. Delete Service
     * ----------------------------------------------------------------------
     *
     * @name   Service Delete
     * @route  DELETE /api/v1/services/{service}
     *
     * @description
     * Soft or permanent deletes a service record and flushes relevant cache tags.
     *
     * @urlParams
     * - service  (int)  The ID of the service
     */
    Route::delete('{service}', [ServiceController::class, 'destroy'])
        ->name('services.destroy');


    /*
     * ----------------------------------------------------------------------
     * 6. Update Activation State
     * ----------------------------------------------------------------------
     *
     * @name   Service Update Activation
     * @route  PUT /api/v1/services/{service}/updateActivation
     *
     * @description
     * Toggles the is_active state of a service record and flushes related cache tags.
     *
     * @bodyParams
     * - is_active  (bool|required)  New activation state
     *
     * @return
     * Updated Service JSON resource.
     */
    Route::put('{service}/updateActivation', [ServiceController::class, 'updateActivation'])
        ->name('services.updateActivation');
});
