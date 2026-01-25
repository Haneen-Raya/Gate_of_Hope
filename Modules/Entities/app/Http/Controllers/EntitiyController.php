<?php

namespace Modules\Entities\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Entities\Http\Requests\Entity\StoreEntityRequest;
use Modules\Entities\Http\Requests\Entity\UpdateEntityRequest;
use Modules\Entities\Models\Entitiy;
use Modules\Entities\Services\EntityService;

class EntitiyController extends Controller
{
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
}
