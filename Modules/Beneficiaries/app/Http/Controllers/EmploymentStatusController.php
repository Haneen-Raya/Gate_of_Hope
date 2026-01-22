<?php

namespace Modules\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Beneficiaries\Http\Requests\EmploymentStatus\StoreEmploymentStatusRequest;
use Modules\Beneficiaries\Http\Requests\EmploymentStatus\UpdateEmploymentStatusActivationRequest;
use Modules\Beneficiaries\Http\Requests\EmploymentStatus\UpdateEmploymentStatusRequest;
use Modules\Beneficiaries\Http\Requests\FilterRequest;
use Modules\Beneficiaries\Models\EmploymentStatus;
use Modules\Beneficiaries\Services\EmploymentStatusService;

class EmploymentStatusController extends Controller
{
    protected EmploymentStatusService $employmentStatusService;

    /**
     * Constructor for the EmploymentStatusController class.
     * Initializes the $EmploymentStatusService property via dependency injection.
     *
     * @param EmploymentStatusService $employmentStatusService
     */
    public function __construct(EmploymentStatusService $employmentStatusService)
    {
        $this->employmentStatusService = $employmentStatusService;
    }

    /**
     * This method return all employment statuses from database.
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
            $this->employmentStatusService->getAllEmploymentStatuses($filters),
            200
        );
    }

    /**
     * Add a new employment status to the database using the EmploymentStatusService via the createEmploymentStatus method
     * passes the validated request data to createEmploymentStatus.
     *
     * @param StoreEmploymentStatusRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEmploymentStatusRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->employmentStatusService->createEmploymentStatus($request->validated()),
            201
        );
    }

    /**
     * Get employment status from database.
     * using the employmentStatusService via the showEmploymentStatus method
     *
     * @param EmploymentStatus $employmentStatus
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(EmploymentStatus $employmentStatus)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->employmentStatusService->showEmploymentStatus($employmentStatus),
            200
        );
    }

    /**
     * Update a employment status in the database using the employmentStatusService via the updateEmploymentStatus method.
     * passes the validated request data to updateEmploymentStatus.
     *
     * @param UpdateEmploymentStatusRequest $request
     * @param EmploymentStatus $employmentStatus
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEmploymentStatusRequest $request, EmploymentStatus $employmentStatus)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->employmentStatusService->updateEmploymentStatus($request->validated(), $employmentStatus)
        );
    }

    /**
     * Remove the specified employment status from database.
     *
     * @param EmploymentStatus $employmentStatus
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(EmploymentStatus $employmentStatus)
    {
        $this->employmentStatusService->deleteEmploymentStatus($employmentStatus);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }

    /**
     * Update the activation status of a specific employment status.
     *
     * Validates the activation data and delegates the update process
     * to the employmentStatusService.
     *
     * @param  UpdateEmploymentStatusActivationRequest  $request
     * @param  EmploymentStatus  $employmentStatus
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActivation(UpdateEmploymentStatusActivationRequest $request, EmploymentStatus $employmentStatus)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->employmentStatusService->updateActivationStatus($request->validated(), $employmentStatus)
        );
    }
}
