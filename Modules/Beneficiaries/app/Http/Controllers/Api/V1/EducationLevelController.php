<?php

namespace Modules\Beneficiaries\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Beneficiaries\Http\Requests\EducationLevel\StoreEducationLevelRequest;
use Modules\Beneficiaries\Http\Requests\EducationLevel\UpdateEducationLevelActivationRequest;
use Modules\Beneficiaries\Http\Requests\EducationLevel\UpdateEducationLevelRequest;
use Modules\Beneficiaries\Http\Requests\FilterRequest;
use Modules\Beneficiaries\Models\EducationLevel;
use Modules\Beneficiaries\Services\EducationLevelService;

class EducationLevelController extends Controller
{
    protected EducationLevelService $educationLevelService;

    /**
     * Constructor for the EducationLevelController class.
     * Initializes the $educationLevelService property via dependency injection.
     *
     * @param EducationLevelService $educationLevelService
     */
    public function __construct(EducationLevelService $educationLevelService)
    {
        $this->educationLevelService = $educationLevelService;
    }

    /**
     * This method return all education levels from database.
     *
     * @param FilterRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FilterRequest $request)
    {
        $filters = $request->validated();
        return $this->successResponse(
            'Operation succcessful',
            $this->educationLevelService->getAllEducationLevels($filters),
            200
        );
    }

    /**
     * Add a new education level to the database using the educationLevelService via the createEducationLevel method
     * passes the validated request data to createEducationLevel.
     *
     * @param StoreEducationLevelRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEducationLevelRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->educationLevelService->createEducationLevel($request->validated()),
            201
        );
    }

    /**
     * Get education level from database.
     * using the educationLevelService via the showEducationLevel method
     *
     * @param EducationLevel $educationLevel
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(EducationLevel $educationLevel)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->educationLevelService->showEducationLevel($educationLevel),
            200
        );
    }

    /**
     * Update a education level in the database using the educationLevelService via the updateEducationLevel method.
     * passes the validated request data to updateEducationLevel.
     *
     * @param UpdateEducationLevelRequest $request
     * @param EducationLevel $educationLevel
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEducationLevelRequest $request, EducationLevel $educationLevel)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->educationLevelService->updateEducationLevel($request->validated(), $educationLevel)
        );
    }

    /**
     * Remove the specified education level from database.
     *
     * @param EducationLevel $educationLevel
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(EducationLevel $educationLevel)
    {
        $this->educationLevelService->deleteEducationLevel($educationLevel);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }

    /**
     * Update the activation status of a specific education level.
     *
     * Validates the activation data and delegates the update process
     * to the EducationLevelService.
     *
     * @param  UpdateEducationLevelActivationRequest  $request
     * @param  EducationLevel  $educationLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActivation(UpdateEducationLevelActivationRequest $request, EducationLevel $educationLevel)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->educationLevelService->updateActivationStatus($request->validated(), $educationLevel)
        );
    }
}
