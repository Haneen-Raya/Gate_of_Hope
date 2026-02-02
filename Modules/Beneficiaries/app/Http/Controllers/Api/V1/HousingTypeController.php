<?php

namespace Modules\Beneficiaries\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Beneficiaries\Http\Requests\Api\V1\HousingType\StoreHousingTypeRequest;
use Modules\Beneficiaries\Http\Requests\Api\V1\HousingType\UpdateHousingTypeActivationRequest;
use Modules\Beneficiaries\Http\Requests\Api\V1\HousingType\UpdateHousingTypeRequest;
use Modules\Beneficiaries\Models\HousingType;
use Modules\Beneficiaries\Services\HousingTypeService;

class HousingTypeController extends Controller
{
    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:housing_types.create', only: ['store']),
            new Middleware('can:housing_types.read', only: ['index','show']),
            new Middleware('can:housing_types.update', only: ['update']),
            new Middleware('can:housing_types.activation.update', only: ['updateActivation']),
            new Middleware('can:housing_types.delete', only: ['destroy']),
        ];
    }

    protected HousingTypeService $housingTypeService;

    /**
     * Constructor for the HousingTypeController class.
     * Initializes the $housingTypeService property via dependency injection.
     *
     * @param HousingTypeService $housingTypeService
     */
    public function __construct(HousingTypeService $housingTypeService)
    {
        $this->housingTypeService = $housingTypeService;
    }

    /**
     * This method return all housing types from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        return $this->successResponse(
            'Operation succcessful',
            $this->housingTypeService->getAllHousingTypes($filters),
            200
        );
    }

    /**
     * Add a new housing type to the database using the housingTypeService via the createHousingType method
     * passes the validated request data to createHousingType.
     *
     * @param StoreHousingTypeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreHousingTypeRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->housingTypeService->createHousingType($request->validated()),
            201
        );
    }

    /**
     * Get housing type from database.
     * using the housingTypeService via the showHousingType method
     *
     * @param HousingType $housingType
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(HousingType $housingType)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->housingTypeService->showHousingType($housingType),
            200
        );
    }

    /**
     * Update a housing type in the database using the housingTypeService via the updateHousingType method.
     * passes the validated request data to updateHousingType.
     *
     * @param UpdateHousingTypeRequest $request
     * @param HousingType $housingType
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateHousingTypeRequest $request, HousingType $housingType)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->housingTypeService->updateHousingType($request->validated(), $housingType)
        );
    }

    /**
     * Remove the specified housing type from database.
     *
     * @param HousingType $housingType
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(HousingType $housingType)
    {
        $this->housingTypeService->deleteHousingType($housingType);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }

    /**
     * Update the activation status of a specific housing type.
     *
     * Validates the activation data and delegates the update process
     * to the housingTypeService.
     *
     * @param  UpdateHousingTypeActivationRequest  $request
     * @param  HousingType  $housingType
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActivation(UpdateHousingTypeActivationRequest $request, HousingType $housingType)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->housingTypeService->updateActivationStatus($request->validated(), $housingType)
        );
    }
}
