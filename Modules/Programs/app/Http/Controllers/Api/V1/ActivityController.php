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

class ActivityController extends Controller
{
    use AuthorizesRequests;

    /**
     * Summary of middleware
     * @return array<Middleware|string>
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
     * @param Request $request
     *
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
     * Add a new activity to the database using the activityService via the createActivity method
     * passes the validated request data to createActivity.
     *
     * @param StoreActivityRequest $request
     *
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
     * using the activityService via the showActivity method
     *
     * @param Activity $activity
     *
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
     * Update a activity in the database using the activityService via the updateActivity method.
     * passes the validated request data to updateActivity.
     *
     * @param UpdateActivityRequest $request
     * @param Activity $activity
     *
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
     * @param Activity $activity
     *
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
     * Validates the activation data and delegates the update process
     * to the activityService.
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
