<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Programs\Models\ProgramResource;
use Modules\Programs\Services\ProgramResourceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Programs\Http\Requests\V1\ProgramResource\StoreProgramResourceRequest;
use Modules\Programs\Http\Requests\V1\ProgramResource\UpdateProgramResourceRequest;

/**
 * Class ProgramResourceController
 *
 * API Controller managing the allocation and lifecycle of program resources.
 * It coordinates with ProgramResourceService for business logic and budget checks,
 * while enforcing security through ProgramResourcePolicy.
 *
 * @package Modules\Programs\Http\Controllers\Api\V1
 * @version 1.0.0
 */
class ProgramResourceController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    /**
     * Define the middleware for the controller.
     *
     * Integrates with Laravel's gate system to authorize actions based on ProgramResourcePolicy.
     *
     * @return array<int, Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,Modules\Programs\Models\ProgramResource', only: ['index']),
            new Middleware('can:view,resource', only: ['show']),
            new Middleware('can:create,Modules\Programs\Models\ProgramResource', only: ['store']),
            new Middleware('can:update,resource', only: ['update']),
            new Middleware('can:delete,resource', only: ['destroy']),
        ];
    }

    /**
     * ProgramResourceController constructor.
     *
     * @param ProgramResourceService $service The service layer handling calculations and storage.
     */
    public function __construct(private ProgramResourceService $service)
    {
    }

    /**
     * Display a paginated list of program resources.
     *
     * @path GET /api/v1/program-resources
     * @param Request $request Filter parameters: name, type, program_id.
     * @return JsonResponse Standard success response with resource collection.
     */
    public function index(Request $request): JsonResponse
    {
        $resources = $this->service->list($request->all());

        return $this->successResponse(
            'Resources retrieved successfully',
            $resources
        );
    }

    /**
     * Store a newly allocated resource and validate program budget.
     *
     * @path POST /api/v1/program-resources
     * @param StoreProgramResourceRequest $request Validated data for resource allocation.
     * @return JsonResponse 201 Created on success, or 422 if budget is exceeded.
     */
    public function store(StoreProgramResourceRequest $request): JsonResponse
    {
        $resource = $this->service->store($request->validated());

        if (!$resource) {
            return $this->errorResponse('Could not create resource. Budget might be exceeded.', null, 422);
        }

        return $this->successResponse(
            'Resource allocated successfully',
            $resource,
            201
        );
    }

    /**
     * Display detailed information for a specific resource.
     *
     * @path GET /api/v1/program-resources/{id}
     * @param int $id Unique identifier of the resource.
     * @return JsonResponse Detailed resource model.
     */
    public function show(int $id): JsonResponse
    {
        $resource = $this->service->getById($id);

        return $this->successResponse(
            'Resource details retrieved successfully',
            $resource
        );
    }

    /**
     * Update an existing resource's information and re-evaluate budget.
     *
     * @path PUT /api/v1/program-resources/{resource}
     * @param UpdateProgramResourceRequest $request Validated update payload.
     * @param ProgramResource $resource Route-bound resource model instance.
     * @return JsonResponse Updated resource or error on budget overflow.
     */
    public function update(UpdateProgramResourceRequest $request, ProgramResource $resource): JsonResponse
    {
        $updated = $this->service->update($resource, $request->validated());

        if (!$updated) {
            return $this->errorResponse('Update failed. Budget exceeded.', null, 422);
        }

        return $this->successResponse(
            'Resource updated successfully',
            $updated
        );
    }

    /**
     * Remove a resource from the system.
     *
     * @path DELETE /api/v1/program-resources/{resource}
     * @param ProgramResource $resource Route-bound resource model instance.
     * @return JsonResponse Success notification.
     */
    public function destroy(ProgramResource $resource): JsonResponse
    {
        $this->service->delete($resource);

        return $this->successResponse('Resource deleted successfully', null, 200);
    }
}
