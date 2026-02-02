<?php

namespace Modules\Programs\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Programs\Http\Requests\V1\ActivitySession\NearbyActivitySessionRequest;
use Modules\Programs\Models\ActivitySession;
use Modules\Programs\Services\ActivitySessionService;
use Modules\Programs\Http\Requests\V1\ActivitySession\StoreActivitySessionRequest;
use Modules\Programs\Http\Requests\V1\ActivitySession\UpdateActivitySessionRequest;

/**
 * Class ActivitySessionController
 *
 * Handles all HTTP requests related to Activity Sessions.
 * Acts as a thin layer between the HTTP layer and the business logic
 * implemented inside ActivitySessionService.
 */
class ActivitySessionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Activity session service instance.
     *
     * @var ActivitySessionService
     */
    protected ActivitySessionService $service;

    /**
     * ActivitySessionController constructor.
     *
     * @param ActivitySessionService $service
     */
    public function __construct(ActivitySessionService $service)
    {
        $this->service = $service;
    }

    /**
     * Retrieve a paginated list of activity sessions.
     *
     * Query Parameters:
     * - per_page (int, optional): Number of items per page (default: 15)
     * - page (int, optional): Current page number (default: 1)
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', ActivitySession::class);

        $perPage = (int) request()->get('per_page', 15);
        $page    = (int) request()->get('page', 1);

        $sessions = $this->service->getPaginatedSessions($perPage, $page);

        return $this->successResponse(
            'Activity sessions retrieved successfully',
            $sessions
        );
    }

    /**
     * Retrieve a single activity session.
     *
     * @param ActivitySession $activitySession
     * @return JsonResponse
     */
    public function show(ActivitySession $activitySession): JsonResponse
    {
        $this->authorize('view', $activitySession);

        return $this->successResponse(
            'Activity session retrieved successfully',
            $activitySession
        );
    }

    /**
     * Create a new activity session.
     *
     * @param StoreActivitySessionRequest $request
     * @return JsonResponse
     */
    public function store(StoreActivitySessionRequest $request): JsonResponse
    {
        $this->authorize('create', ActivitySession::class);

        $session = $this->service->create($request->validated());

        return $this->successResponse(
            'Activity session created successfully',
            $session,
            201
        );
    }

    /**
     * Update an existing activity session.
     *
     * @param UpdateActivitySessionRequest $request
     * @param ActivitySession $activitySession
     * @return JsonResponse
     */
    public function update(
        UpdateActivitySessionRequest $request,
        ActivitySession $activitySession
    ): JsonResponse {
        $this->authorize('update', $activitySession);

        $session = $this->service->update(
            $activitySession,
            $request->validated()
        );

        return $this->successResponse(
            'Activity session updated successfully',
            $session
        );
    }

    /**
     * Delete an activity session.
     *
     * @param ActivitySession $activitySession
     * @return JsonResponse
     */
    public function destroy(ActivitySession $activitySession): JsonResponse
    {
        $this->authorize('delete', $activitySession);

        $this->service->delete($activitySession);

        return $this->successResponse(
            'Activity session deleted successfully'
        );
    }

    /**
     * Mark an activity session as completed.
     *
     * @param ActivitySession $activitySession
     * @return JsonResponse
     */
    public function complete(ActivitySession $activitySession): JsonResponse
    {
        $this->authorize('update', $activitySession);

        $session = $this->service->complete($activitySession);

        return $this->successResponse(
            'Activity session marked as completed',
            $session
        );
    }

    /**
     * Cancel an activity session.
     *
     * @param ActivitySession $activitySession
     * @return JsonResponse
     */
    public function cancel(ActivitySession $activitySession): JsonResponse
    {
        $this->authorize('update', $activitySession);

        $session = $this->service->cancel($activitySession);

        return $this->successResponse(
            'Activity session cancelled',
            $session
        );
    }
    /**
     * Retrieve nearby activity sessions based on geographic location.
     *
     * This endpoint returns activity sessions within a given radius
     * from the provided latitude and longitude coordinates.
     *
     * Request Parameters:
     * - lat (float, required): Latitude coordinate (-90 to 90).
     * - lng (float, required): Longitude coordinate (-180 to 180).
     * - radius (int, optional): Search radius in meters (default: 5000).
     * - activity_id (int, optional): Filter results by activity ID.
     *
     * @param NearbyActivitySessionRequest $request
     * @return JsonResponse
     */
    public function nearby(NearbyActivitySessionRequest $request): JsonResponse
    {
        $sessions = $this->service->getNearbySessions(
            $request->lat,
            $request->lng,
            $request->radius,
            $request->activity_id
        );

        return $this->successResponse(
            'Nearby activity sessions retrieved successfully',
            $sessions
        );
    }

    /**
     * Retrieve upcoming activity sessions for a specific trainer.
     *
     * @param int $trainer Trainer ID
     * @return JsonResponse
     */
    public function upcomingForTrainer(int $trainer): JsonResponse
    {
        $this->authorize('viewUpcomingForTrainer', [ActivitySession::class, $trainer]);

        $sessions = $this->service->getUpcomingForTrainer($trainer);

        return $this->successResponse(
            'Upcoming activity sessions for trainer retrieved successfully',
            $sessions
        );
    }

    /**
     * Retrieve upcoming activity sessions for a specific activity.
     *
     * @param int $activity Activity ID
     * @return JsonResponse
     */
    public function upcomingForActivity(int $activity): JsonResponse
    {
        $this->authorize('viewUpcomingForActivity', ActivitySession::class);

        $sessions = $this->service->getUpcomingForActivity($activity);

        return $this->successResponse(
            'Upcoming activity sessions for activity retrieved successfully',
            $sessions
        );
    }
}
