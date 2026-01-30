<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Programs\Http\Requests\Api\V1\ActivityAttendance\StoreActivityAttendanceRequest;
use Modules\Programs\Http\Requests\Api\V1\ActivityAttendance\UpdateActivityAttendanceRequest;
use Modules\Programs\Models\ActivityAttendance;
use Modules\Programs\Services\ActivityAttendanceService;

class ActivityAttendanceController extends Controller
{
    protected ActivityAttendanceService $activityAttendanceService;

    /**
     * Constructor for the ActivityAttendanceController class.
     * Initializes the $activityAttendanceService property via dependency injection.
     *
     * @param ActivityAttendanceService $activityAttendanceService
     */
    public function __construct(ActivityAttendanceService $activityAttendanceService)
    {
        $this->activityAttendanceService = $activityAttendanceService;
    }

    /**
     * This method return all activity attendances from database.
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
            $this->activityAttendanceService->getAllActivityAttendances($filters),
            200
        );
    }

    /**
     * Add a new activityAttendance to the database using the activityAttendanceService via the createActivityAttendance method
     * passes the validated request data to createActivityAttendance.
     *
     * @param StoreActivityAttendanceRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreActivityAttendanceRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->activityAttendanceService->createActivityAttendance($request->validated()),
            201
        );
    }

    /**
     * Get activityAttendance from database.
     * using the activityAttendanceService via the showActivityAttendance method
     *
     * @param ActivityAttendance $activityAttendance
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ActivityAttendance $activityAttendance)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->activityAttendanceService->showActivityAttendance($activityAttendance),
            200
        );
    }

    /**
     * Update a activityAttendance in the database using the activityAttendanceService via the updateActivityAttendance method.
     * passes the validated request data to updateActivityAttendance.
     *
     * @param UpdateActivityAttendanceRequest $request
     * @param ActivityAttendance $activityAttendance
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateActivityAttendanceRequest $request, ActivityAttendance $activityAttendance)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->activityAttendanceService->updateActivityAttendance($request->validated(), $activityAttendance)
        );
    }

    /**
     * Remove the specified activityAttendance from database.
     *
     * @param ActivityAttendance $activityAttendance
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ActivityAttendance $activityAttendance)
    {
        $this->activityAttendanceService->deleteActivityAttendance($activityAttendance);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }
}
