<?php

namespace Modules\CaseManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CaseManagement\Http\Requests\Service\StoreServiceRequest;
use Modules\CaseManagement\Http\Requests\Service\UpdateServiceRequest;
use Modules\CaseManagement\Models\Service;
use Modules\CaseManagement\Services\ServiceService;

class ServiceController extends Controller
{
    protected ServiceService $serviceService;

    /**
     * Constructor for the ServiceController class.
     * Initializes the $serviceService property via dependency injection.
     *
     * @param ServiceService $serviceService
     */
    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * This method return all services from database.
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
            $this->serviceService->getAllServices($filters),
            200
        );
    }

    /**
     * Add a new service to the database using the serviceService via the createService method
     * passes the validated request data to createService.
     *
     * @param StoreServiceRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreServiceRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->serviceService->createService($request->validated()),
            201
        );
    }

    /**
     * Get service from database.
     * using the serviceService via the showService method
     *
     * @param Service $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Service $service)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->serviceService->showService($service),
            200
        );
    }

    /**
     * Update a service in the database using the serviceService via the updateService method.
     * passes the validated request data to updateService.
     *
     * @param UpdateServiceRequest $request
     * @param Service $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->serviceService->updateService($request->validated(), $service)
        );
    }

    /**
     * Remove the specified service from database.
     *
     * @param Service $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Service $service)
    {
        $this->serviceService->deleteService($service);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }
}
