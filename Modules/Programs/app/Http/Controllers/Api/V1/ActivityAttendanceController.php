<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Programs\Http\Requests\Api\V1\ActivityAttendance\StoreActivityAttendanceRequest;
use Modules\Programs\Http\Requests\Api\V1\ActivityAttendance\UpdateActivityAttendanceRequest;
use Modules\Programs\Models\ActivityAttendance;
use Modules\Programs\Services\ActivityAttendanceService;

/**
 * Class ActivityAttendanceController
 * * This controller orchestrates the attendance tracking for various program activities.
 * It serves as a bridge between the API consumers and the underlying Attendance Service Layer.
 * * KEY ARCHITECTURE:
 * - Middleware-based Authorization: Enforces granular permissions for CRUD operations.
 * - Service Pattern: Delegates business logic to the ActivityAttendanceService.
 * - Form Request Validation: Offloads data integrity checks to specialized request classes.
 * * @package Modules\Programs\Http\Controllers\Api\V1
 */
class ActivityAttendanceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Middleware Registration
     * * Registers permission-based guards for each controller method using the 'can' gate.
     * This ensures that only authorized staff can create, read, or modify attendance records.
     *
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:activity.attendance.create', only: ['store']),
            new Middleware('can:activity.attendance.read', only: ['index','show']),
            new Middleware('can:activity.attendance.update', only: ['update']),
            new Middleware('can:activity.attendance.delete', only: ['destroy']),
        ];
    }

    /**
     * @var ActivityAttendanceService Encapsulated business logic service.
     */
    protected ActivityAttendanceService $activityAttendanceService;

    /**
     * Constructor Injection
     * * Initializes the controller with its primary service dependency.
     * Injection via constructor ensures high testability and loose coupling.
     *
     * @param ActivityAttendanceService $activityAttendanceService
     */
    public function __construct(ActivityAttendanceService $activityAttendanceService)
    {
        $this->activityAttendanceService = $activityAttendanceService;
    }

    /**
     * Display a paginated/filtered list of activity attendances.
     * * Retrieves all attendance records based on the request's query parameters.
     * The processing is offloaded to the service layer for query optimization.
     *
     * @param Request $request
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
     * Persist a new attendance record.
     * * Validates input via StoreActivityAttendanceRequest and creates a new
     * record in the database through the service layer.
     *
     * @param StoreActivityAttendanceRequest $request
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
     * Retrieve a specific attendance record.
     * * Utilizes Route Model Binding to fetch the instance and checks for
     * granular Policy-based authorization before returning the data.
     *
     * @param ActivityAttendance $activityAttendance
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ActivityAttendance $activityAttendance)
    {
        $this->authorize('view', $activityAttendance);
        return $this->successResponse(
            'Operation succcessful',
            $this->activityAttendanceService->showActivityAttendance($activityAttendance),
            200
        );
    }

    /**
     * Update an existing attendance record.
     * * Performs a policy check followed by data validation. The sanitized
     * data is then passed to the service layer to perform the update.
     *
     * @param UpdateActivityAttendanceRequest $request
     * @param ActivityAttendance $activityAttendance
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateActivityAttendanceRequest $request, ActivityAttendance $activityAttendance)
    {
        $this->authorize('update', $activityAttendance);
        return $this->successResponse(
            'Updated succcessful',
            $this->activityAttendanceService->updateActivityAttendance($request->validated(), $activityAttendance)
        );
    }

    /**
     * Delete an attendance record.
     * * Physically or softly removes the specified attendance instance
     * through the service layer.
     *
     * @param ActivityAttendance $activityAttendance
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
