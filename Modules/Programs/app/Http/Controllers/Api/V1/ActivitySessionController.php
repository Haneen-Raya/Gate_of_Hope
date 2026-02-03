<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Programs\Http\Requests\V1\ActivitySession\NearbyActivitySessionRequest;
use Modules\Programs\Models\ActivitySession;
use Modules\Programs\Services\ActivitySessionService;
use Modules\Programs\Http\Requests\V1\ActivitySession\StoreActivitySessionRequest;
use Modules\Programs\Http\Requests\V1\ActivitySession\UpdateActivitySessionRequest;
use Modules\Programs\Http\Resources\ActivitySessionResource;

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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ActivitySession::class);

        $perPage = (int) request()->get('per_page', 15);
        $page    = (int) request()->get('page', 1);

        $sessions = $this->service->getPaginatedSessions($perPage, $page);

        // ➤ استخدام الريسورس لكل عنصر في الصفحة
        return ActivitySessionResource::collection($sessions)
            ->additional(['success' => true, 'message' => 'Activity sessions retrieved successfully']);
    }


    /**
     * Retrieve a single activity session.
     *
     * @param ActivitySession $activitySession
     * @return ActivitySessionResource
     */
    public function show(ActivitySession $activitySession): ActivitySessionResource
    {
        $this->authorize('view', $activitySession);

        return (new ActivitySessionResource($activitySession))
            ->additional(['success' => true, 'message' => 'Activity session retrieved successfully']);
    }

  /**
     * Store a new activity session.
     *
     * @param StoreActivitySessionRequest $request
     * @return ActivitySessionResource
     */
    public function store(StoreActivitySessionRequest $request): ActivitySessionResource
    {
        $this->authorize('create', ActivitySession::class);

        $session = $this->service->create($request->validated());

        return (new ActivitySessionResource($session))
            ->additional(['success' => true, 'message' => 'Activity session created successfully']);
    }

       /**
     * Update an existing activity session.
     *
     * @param UpdateActivitySessionRequest $request
     * @param ActivitySession $activitySession
     * @return ActivitySessionResource
     */
    public function update(UpdateActivitySessionRequest $request, ActivitySession $activitySession): ActivitySessionResource
    {
        $this->authorize('update', $activitySession);

        $session = $this->service->update($activitySession, $request->validated());

        return (new ActivitySessionResource($session))
            ->additional(['success' => true, 'message' => 'Activity session updated successfully']);
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

        return $this->successResponse('Activity session deleted successfully');
    }

    /**
     * Complete an activity session.
     *
     * @param ActivitySession $activitySession
     * @return ActivitySessionResource
     */
    public function complete(ActivitySession $activitySession): ActivitySessionResource
    {
        $this->authorize('update', $activitySession);

        $session = $this->service->complete($activitySession);

        return (new ActivitySessionResource($session))
            ->additional(['success' => true, 'message' => 'Activity session marked as completed']);
    }

    /**
     * Cancel an activity session.
     *
     * @param ActivitySession $activitySession
     * @return ActivitySessionResource
     */
    public function cancel(ActivitySession $activitySession): ActivitySessionResource
    {
        $this->authorize('update', $activitySession);

        $session = $this->service->cancel($activitySession);

        return (new ActivitySessionResource($session))
            ->additional(['success' => true, 'message' => 'Activity session cancelled']);
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
   public function nearby(NearbyActivitySessionRequest $request): AnonymousResourceCollection
    {
        $sessions = $this->service->getNearbySessions(
            $request->lat,
            $request->lng,
            $request->radius,
            $request->activity_id
        );

        return ActivitySessionResource::collection($sessions)
            ->additional(['success' => true, 'message' => 'Nearby activity sessions retrieved successfully']);
    }

    /**
     * Retrieve upcoming activity sessions for a specific trainer.
     *
     * @param int $trainer Trainer ID
     * @return JsonResponse
     */
    public function upcomingForTrainer(int $trainer): AnonymousResourceCollection
    {
        $this->authorize('viewUpcomingForTrainer', [ActivitySession::class, $trainer]);

        $sessions = $this->service->getUpcomingForTrainer($trainer);

        return ActivitySessionResource::collection($sessions)
            ->additional(['success' => true, 'message' => 'Upcoming activity sessions for trainer retrieved successfully']);
    }

    /**
     * Retrieve upcoming activity sessions for a specific activity.
     *
     * @param int $activity Activity ID
     * @return AnonymousResourceCollection
     */
  public function upcomingForActivity(int $activity): AnonymousResourceCollection
    {
        $this->authorize('viewUpcomingForActivity', ActivitySession::class);

        $sessions = $this->service->getUpcomingForActivity($activity);

        return ActivitySessionResource::collection($sessions)
            ->additional(['success' => true, 'message' => 'Upcoming activity sessions for activity retrieved successfully']);
    }
}
