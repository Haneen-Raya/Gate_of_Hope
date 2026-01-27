<?php 

namespace Modules\HumanResources\Http\Controllers\Api\V1;

use Modules\HumanResources\Models\Specialist;
use Modules\HumanResources\Services\SpecialistService;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\HumanResources\Http\Requests\V1\Specialist\StoreSpecialistRequest;
use Modules\HumanResources\Http\Requests\V1\Specialist\UpdateSpecialistRequest;
use Modules\HumanResources\Policies\SpecialistPolicy;

/**
 * Class SpecialistController
 * @package Modules\HumanResources\Http\Controllers\Api
 *
 * Controller for managing Specialists via API.
 * All responses are formatted using the base Controller's successResponse and errorResponse methods.
 *
 * @group Specialists
 *
 * APIs for managing specialists
 */
class SpecialistController extends Controller
{
    use AuthorizesRequests ;
    protected SpecialistService $service;
    protected SpecialistPolicy $policy;

    /**
     * Constructor
     *
     * @param SpecialistService $service Service class for handling business logic and caching.
     */
    public function __construct(SpecialistService $service , SpecialistPolicy $policy)
    {
        $this->service = $service;
        $this->policy = $policy;
    }

    /**
     * Get all specialists
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Specialists retrieved successfully",
     *   "data": [ {...}, {...} ]
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', Specialist::class);
        $specialists = $this->service->all();
        return $this->successResponse('Specialists retrieved successfully', $specialists);
    }

    /**
     * Get a single specialist
     *
     * @param Specialist $specialist
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Specialist retrieved successfully",
     *   "data": { ... }
     * }
     */
    public function show(Specialist $specialist)
    {
        $specialist->load(['user', 'issueCategory']);
        return $this->successResponse('Specialist retrieved successfully', $specialist);
    }

    /**
     * Create a new specialist
     *
     * @param StoreSpecialistRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Specialist created successfully",
     *   "data": { ... }
     * }
     */
    public function store(StoreSpecialistRequest $request)
    {
        $this->authorize('create', Specialist::class);
        $specialist = $this->service->create($request->validated());
        return $this->successResponse('Specialist created successfully', $specialist, 201);
    }

    /**
     * Update an existing specialist
     *
     * @param UpdateSpecialistRequest $request
     * @param Specialist $specialist
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Specialist updated successfully",
     *   "data": { ... }
     * }
     */
    public function update(UpdateSpecialistRequest $request, Specialist $specialist)
    {
        $this->authorize('update', $specialist);
        $specialist = $this->service->update($specialist, $request->validated());
        return $this->successResponse('Specialist updated successfully', $specialist);
    }

    /**
     * Delete a specialist
     *
     * @param Specialist $specialist
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Specialist deleted successfully",
     *   "data": null
     * }
     */
    public function destroy(Specialist $specialist)
    {
        $this->authorize('delete', $specialist);
        // Prevent deleting specialist if they have related case sessions
        if ($specialist->caseSessions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete specialist because they are linked to existing case sessions.'
            ], 409);
        }

        // Delegate deletion logic to the service layer
        $this->service->delete($specialist);

        return   $this->successResponse(
            'Specialist deleted successfully'
        );
    }

}
