<?php 

namespace Modules\HumanResources\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\HumanResources\Models\Trainer;
use Modules\HumanResources\Services\TrainerService;
use Modules\HumanResources\Policies\TrainerPolicy;
use Modules\HumanResources\Http\Requests\V1\Trainer\StoreTrainerRequest;
use Modules\HumanResources\Http\Requests\V1\Trainer\UpdateTrainerRequest;

/**
 * Class TrainerController
 * @package Modules\HumanResources\Http\Controllers\Api\V1
 *
 * Controller for managing Trainers via API.
 * All responses are formatted using the base Controller's successResponse and errorResponse methods.
 *
 * @group Trainers
 *
 * APIs for managing trainers
 */
class TrainerController extends Controller
{
    use AuthorizesRequests;

    protected TrainerService $service;
    protected TrainerPolicy $policy;

    /**
     * Constructor
     *
     * @param TrainerService $service Service class for handling business logic and caching.
     * @param TrainerPolicy  $policy  Policy class for authorization.
     */
    public function __construct(TrainerService $service, TrainerPolicy $policy)
    {
        $this->service = $service;
        $this->policy  = $policy;
    }

    /**
     * Get all trainers
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Trainers retrieved successfully",
     *   "data": [ {...}, {...} ]
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', Trainer::class);

        $trainers = $this->service->list(request()->all());

        return $this->successResponse(
            'Trainers retrieved successfully',
            $trainers
        );
    }

    /**
     * Get a single trainer
     *
     * @param Trainer $trainer
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Trainer retrieved successfully",
     *   "data": { ... }
     * }
     */
    public function show(Trainer $trainer)
    {
        $trainer = $this->service->show($trainer);

        return $this->successResponse(
            'Trainer retrieved successfully',
            $trainer
        );
    }

    /**
     * Create a new trainer
     *
     * @param StoreTrainerRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Trainer created successfully",
     *   "data": { ... }
     * }
     */
    public function store(StoreTrainerRequest $request)
    {
        $this->authorize('create', Trainer::class);

        $trainer = $this->service->create($request->validated());

        return $this->successResponse(
            'Trainer created successfully',
            $trainer,
            201
        );
    }

    /**
     * Update an existing trainer
     *
     * @param UpdateTrainerRequest $request
     * @param Trainer              $trainer
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Trainer updated successfully",
     *   "data": { ... }
     * }
     */
    public function update(UpdateTrainerRequest $request, Trainer $trainer)
    {
        $this->authorize('update', $trainer);

        $trainer = $this->service->update(
            $trainer,
            $request->validated()
        );

        return $this->successResponse(
            'Trainer updated successfully',
            $trainer
        );
    }

    /**
     * Delete a trainer
     *
     * @param Trainer $trainer
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Trainer deleted successfully",
     *   "data": null
     * }
     */
    public function destroy(Trainer $trainer)
    {
        $this->authorize('delete', $trainer);

        // Prevent deleting trainer if they have related activity sessions
        if ($trainer->activitySessions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete trainer because they are linked to existing activity sessions.'
            ], 409);
        }

        // Delegate deletion logic to the service layer
        $this->service->delete($trainer);

        return $this->successResponse(
            'Trainer deleted successfully'
        );
    }
}
