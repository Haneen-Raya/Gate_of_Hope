<?php

namespace Modules\Entities\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Entities\Http\Requests\Api\V1\Entity\StoreEntityRequest;
use Modules\Entities\Http\Requests\Api\V1\Entity\UpdateEntityRequest;
use Modules\Entities\Models\Entitiy;
use Modules\Entities\Services\EntityService;
use Modules\Programs\Http\Requests\Api\V1\Activity\UpdateActivityActivationRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class EntitiyController
 * * Manages the lifecycle of organizational entities, including NGOs, government bodies,
 * or partner organizations. It handles CRUD operations, activation toggling, and
 * implements strict multi-tier authorization.
 * * @package Modules\Entities\Http\Controllers\Api\V1
 */
class EntitiyController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    /**
     * Define the middleware stack for the controller.
     * * Implement RBAC (Role-Based Access Control) with support for "Self-Service"
     * permissions (e.g., entities.read.self) to allow users to manage their own organizations.
     * * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:entities.create', only: ['store']),
            new Middleware('can:entities.read | entities.read.self', only: ['index', 'show']),
            new Middleware('can:entities.update | entities.update.self', only: ['update']),
            new Middleware('can:entities.activation.update', only: ['updateActivation']),
            new Middleware('can:entities.delete', only: ['destroy']),
        ];
    }

    /**
     * The service layer responsible for handling entity business logic.
     * * @var EntityService
     */
    protected EntityService $entityService;

    /**
     * EntitiyController constructor.
     * * @param EntityService $entityService Injected service for decoupling controller and logic.
     */
    public function __construct(EntityService $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * Fetch all entities based on provided filters.
     * * Access is governed by viewAny policy method.
     * * @param Request $request Contains query parameters for filtering and pagination.
     * @return JsonResponse Returns a collection of Entity resources.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny');
        $filters = $request->all();
        return $this->successResponse(
            'Operation successful',
            $this->entityService->getAllentities($filters),
            200
        );
    }

    /**
     * Persist a new entity record.
     * * @param StoreEntityRequest $request Validated request object for entity creation.
     * @return JsonResponse Returns the newly created entity data.
     */
    public function store(StoreEntityRequest $request): JsonResponse
    {
        return $this->successResponse(
            'Created successful',
            $this->entityService->createEntity($request->validated()),
            201
        );
    }

    /**
     * Retrieve detailed information about a single entity.
     * * @param Entitiy $entity Injected via Route Model Binding.
     * @return JsonResponse Returns a single Entity resource.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Entitiy $entity): JsonResponse
    {
        $this->authorize('view', $entity);
        return $this->successResponse(
            'Operation successful',
            $this->entityService->showEntity($entity),
            200
        );
    }

    /**
     * Update an existing entity's information.
     * * @param UpdateEntityRequest $request Validated request object for partial/full updates.
     * @param Entitiy $entity The entity instance to be modified.
     * @return JsonResponse Returns the updated entity resource.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateEntityRequest $request, Entitiy $entity): JsonResponse
    {
        $this->authorize('update', $entity);
        return $this->successResponse(
            'Updated successful',
            $this->entityService->updateEntity($request->validated(), $entity)
        );
    }

    /**
     * Permanently or soft-delete an entity record from the system.
     * * @param Entitiy $entity The entity target for deletion.
     * @return JsonResponse Standardized success response without payload.
     */
    public function destroy(Entitiy $entity): JsonResponse
    {
        $this->entityService->deleteEntity($entity);
        return $this->successResponse(
            'Deleted successful',
            null
        );
    }

    /**
     * Administrative Action: Toggle activation status of an entity.
     * * Used for enabling/disabling entity access to the system without deletion.
     * * @param UpdateActivityActivationRequest $request Contains the boolean 'is_active' state.
     * @param Entitiy $entity The target entity for status modification.
     * @return JsonResponse Returns the updated entity status.
     */
    public function updateActivation(UpdateActivityActivationRequest $request, Entitiy $entity): JsonResponse
    {
        return $this->successResponse(
            'Updated successful',
            $this->entityService->updateActivationStatus($request->validated(), $entity)
        );
    }
}
