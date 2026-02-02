<?php

namespace Modules\Entities\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Entities\Http\Requests\Api\V1\Entity\StoreEntityRequest;
use Modules\Entities\Http\Requests\Api\V1\Entity\UpdateEntityRequest;
use Modules\Entities\Models\Entitiy;
use Modules\Entities\Services\EntityService;
use Modules\Programs\Http\Requests\Api\V1\Activity\UpdateActivityActivationRequest;

class EntitiyController extends Controller
{
    use AuthorizesRequests;
    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:entities.create', only: ['store']),
            new Middleware('can:entities.read | entities.read.self', only: ['index','show']),
            new Middleware('can:entities.update| entities.update.self', only: ['update']),
            new Middleware('can:entities.activation.update', only: ['updateActivation']),
            new Middleware('can:entities.delete', only: ['destroy']),
        ];
    }

    protected EntityService $entityService;

    /**
     * Constructor for the EntitiyController class.
     * Initializes the $entityService property via dependency injection.
     *
     * @param EntityService $entityService
     */
    public function __construct(EntityService $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * This method return all entities from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny');
        $filters = $request->all();
        return $this->successResponse(
            'Operation succcessful',
            $this->entityService->getAllentities($filters),
            200
        );
    }

    /**
     * Add a new entity to the database using the entityService via the createEntity method
     * passes the validated request data to createEntity.
     *
     * @param StoreEntityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEntityRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->entityService->createEntity($request->validated()),
            201
        );
    }

    /**
     * Get entity from database.
     * using the entityService via the showEntity method
     *
     * @param Entitiy $entity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Entitiy $entity)
    {
        $this->authorize('view', $entity);
        return $this->successResponse(
            'Operation succcessful',
            $this->entityService->showEntity($entity),
            200
        );
    }

    /**
     * Update a entity in the database using the entityService via the updateEntity method.
     * passes the validated request data to updateEntity.
     *
     * @param UpdateEntityRequest $request
     * @param Entitiy $entity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEntityRequest $request, Entitiy $entity)
    {
        $this->authorize('update', $entity);
        return $this->successResponse(
            'Updated succcessful',
            $this->entityService->updateEntity($request->validated(), $entity)
        );
    }

    /**
     * Remove the specified entity from database.
     *
     * @param Entitiy $entity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Entitiy $entity)
    {
        $this->entityService->deleteEntity($entity);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }

    /**
     * Update the activation status of a specific entity.
     *
     * Validates the activation data and delegates the update process
     * to the entityService.
     *
     * @param  UpdateActivityActivationRequest  $request
     * @param  Entitiy $entity
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActivation(UpdateActivityActivationRequest $request, Entitiy $entity)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->entityService->updateActivationStatus($request->validated(), $entity)
        );
    }
}
