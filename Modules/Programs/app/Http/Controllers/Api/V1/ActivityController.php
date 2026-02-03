<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Programs\Http\Requests\Api\V1\Activity\StoreActivityRequest;
use Modules\Programs\Http\Requests\Api\V1\Activity\UpdateActivityActivationRequest;
use Modules\Programs\Http\Requests\Api\V1\Activity\UpdateActivityRequest;
use Modules\Programs\Models\Activity;
use Modules\Programs\Services\ActivityService;

/**
 * Class ActivityController
 *
 * This controller handles the lifecycle of educational and support activities within the Programs module.
 * It integrates granular permission middleware, service-layer delegation, and policy-based authorization.
 *
 * @package Modules\Programs\Http\Controllers\Api\V1
 */
class ActivityController extends Controller
{
    use AuthorizesRequests;

    /**
     * Summary of middleware
     *
     * Defines the security layer for the controller, mapping specific spatie-permissions
     * to API endpoints to ensure strict access control.
     * * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:activities.create', only: ['store']),
            new Middleware('can:activities.read', only: ['index','show']),
            new Middleware('can:activities.update', only: ['update']),
            new Middleware('can:activities.activation.update', only: ['update']),
            new Middleware('can:activities.delete', only: ['destroy']),
        ];
    }

    /**
     * @var ActivityService The service responsible for business logic execution.
     */
    protected ActivityService $activityService;

    /**
     * Constructor for the ActivityController class.
     * Initializes the $activityService property via dependency injection.
     *
     * @param ActivityService $activityService
     */
    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * This method return all activities from database.
     *
     * Fetches a collection of activities filtered by the request parameters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        return $this->successResponse(
            'Operation succcessful',
            $this->activityService->getAllActivities($filters),
            200
        );
    }

    /**
     * Add a new activity to the database.
     * * Passes the validated request data to the service layer for record creation.
     *
     * @param StoreActivityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreActivityRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->activityService->createActivity($request->validated()),
            201
        );
    }

    /**
     * Get activity from database.
     * * Retrieves a single activity instance with policy-based authorization check.
     *
     * @param Activity $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Activity $activity)
    {
        $this->authorize('view', $activity);
        return $this->successResponse(
            'Operation succcessful',
            $this->activityService->showActivity($activity),
            200
        );
    }

    /**
     * Update a activity in the database.
     * * Authorized update process using validated data through the ActivityService.
     *
     * @param UpdateActivityRequest $request
     * @param Activity $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $this->authorize('update', $activity);
        return $this->successResponse(
            'Updated succcessful',
            $this->activityService->updateActivity($request->validated(), $activity)
        );
    }

    /**
     * Remove the specified activity from database.
     *
     * Ensures the user has deletion rights for the specific model instance.
     *
     * @param Activity $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Activity $activity)
    {
        $this->authorize('delete', $activity);
        $this->activityService->deleteActivity($activity);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }

    /**
     * Update the activation status of a specific activity.
     *
     * Special endpoint for toggling the 'active' state of an activity.
     *
     * @param  UpdateActivityActivationRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActivation(UpdateActivityActivationRequest $request, Activity $activity)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->activityService->updateActivationStatus($request->validated(), $activity)
        );
    }
}
