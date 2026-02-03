<?php

namespace Modules\Entities\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Entities\Http\Requests\Api\V1\ProgramFunding\StoreProgramFundingRequest;
use Modules\Entities\Http\Requests\Api\V1\ProgramFunding\UpdateProgramFundingRequest;
use Modules\Entities\Models\ProgramFunding;
use Modules\Entities\Services\ProgramFundingService;
use Illuminate\Http\JsonResponse;

/**
 * Class ProgramFundingController
 * * Orchestrates the administrative and financial operations for Program Funding.
 * This controller acts as a bridge between the API consumers and the ProgramFundingService,
 * ensuring strict adherence to authorization policies and request validation.
 * * @package Modules\Entities\Http\Controllers\Api\V1
 */
class ProgramFundingController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    /**
     * Define the middleware stack for the controller.
     * Maps specific permissions to API endpoints to ensure Role-Based Access Control (RBAC).
     * * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:program.funding.create', only: ['store']),
            new Middleware('can:program.funding.read', only: ['index', 'show']),
            new Middleware('can:program.funding.update', only: ['update']),
            new Middleware('can:program.funding.delete', only: ['destroy']),
        ];
    }

    /**
     * The service instance handling the core domain logic for program funding.
     * * @var ProgramFundingService
     */
    protected ProgramFundingService $programFundingService;

    /**
     * ProgramFundingController constructor.
     * Performs dependency injection of the ProgramFundingService.
     *
     * @param ProgramFundingService $programFundingService
     */
    public function __construct(ProgramFundingService $programFundingService)
    {
        $this->programFundingService = $programFundingService;
    }

    /**
     * Retrieve a filtered and paginated list of all program fundings.
     * * @param Request $request The incoming HTTP request containing optional filters.
     * @return JsonResponse Returns a collection of ProgramFunding resources.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        return $this->successResponse(
            'Operation successful',
            $this->programFundingService->getAllProgramFundings($filters),
            200
        );
    }

    /**
     * Store a newly created program funding record in the database.
     * * Validates the input via StoreProgramFundingRequest and delegates
     * the persistence logic to the service layer.
     *
     * @param StoreProgramFundingRequest $request Specialized request for creation validation.
     * @return JsonResponse Returns the newly created ProgramFunding resource.
     */
    public function store(StoreProgramFundingRequest $request): JsonResponse
    {
        return $this->successResponse(
            'Created successful',
            $this->programFundingService->createProgramFunding($request->validated()),
            201
        );
    }

    /**
     * Display the specified program funding details.
     * * Utilizes Route Model Binding to fetch the instance and verifies
     * ownership/view permissions via the explicit authorize method.
     *
     * @param ProgramFunding $programFunding The injected model instance.
     * @return JsonResponse Returns the detailed ProgramFunding resource.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(ProgramFunding $programFunding): JsonResponse
    {
        $this->authorize('view', $programFunding);
        return $this->successResponse(
            'Operation successful',
            $this->programFundingService->showProgramFunding($programFunding),
            200
        );
    }

    /**
     * Update an existing program funding record.
     * * Processes partial or full updates based on validated data from UpdateProgramFundingRequest.
     *
     * @param UpdateProgramFundingRequest $request Specialized request for update validation.
     * @param ProgramFunding $programFunding The existing model instance to be updated.
     * @return JsonResponse Returns the updated ProgramFunding resource.
     */
    public function update(UpdateProgramFundingRequest $request, ProgramFunding $programFunding): JsonResponse
    {
        return $this->successResponse(
            'Updated successful',
            $this->programFundingService->updateProgramFunding($request->validated(), $programFunding)
        );
    }

    /**
     * Remove the specified program funding from the persistent storage.
     * * Performs a soft or hard delete via the service layer depending on system configuration.
     *
     * @param ProgramFunding $programFunding The model instance to be deleted.
     * @return JsonResponse Returns a confirmation of the deletion.
     */
    public function destroy(ProgramFunding $programFunding): JsonResponse
    {
        $this->programFundingService->deleteProgramFunding($programFunding);
        return $this->successResponse(
            'Deleted successful',
            null
        );
    }
}
