<?php

namespace Modules\CaseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CaseManagement\Http\Requests\Api\V1\CasePlanGoal\StoreCasePlanGoalRequest;
use Modules\CaseManagement\Http\Requests\Api\V1\CasePlanGoal\UpdateCasePlanGoalRequest;
use Modules\CaseManagement\Models\CasePlanGoal;
use Modules\CaseManagement\Services\CasePlanGoalService;
use Symfony\Component\HttpFoundation\JsonResponse;

class CasePlanGoalController extends Controller
{
    /**
     * Service to handle case-plan-goal-related logic 
     * and separating it from the controller
     *
     * @var CasePlanGoalService
     */
    protected $casePlanGoalService;

    /**
     * CasePlanGoalController constructor
     *
     * @param CasePlanGoalService $casePlanGoalService
     */
    public function __construct(CasePlanGoalService $casePlanGoalService)
    {
        // Inject the CasePlanGoalService to handle case-plan-goal-related logic
        $this->casePlanGoalService = $casePlanGoalService;
    }

    /**
     * Display a paginated listing of case plan goals with dynamic filtering.
     *
     * Workflow:
     * 1. **Capture Filters:** Collects dynamic query parameters (e.g., case_id, version).
     * 2. **Service Delegation:** Passes the filtering array to the Service Layer, 
     * which manages the complex tagged caching and database execution.
     *
     * @param Request $request Incoming request with potential filter parameters.
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // 1. Filter Extraction: Gather all dynamic filter criteria from the request.
        $filters = $request->all();

        // 2. Execution: Fetch cached and filtered data through the service layer.
        $casePlanGoals = $this->casePlanGoalService->list($filters);

        // 3. Response
        return self::successResponse('Case Plan Goals fetched successfully', $casePlanGoals);
    }

    /**
     * Store a newly created case plan goal in storage.
     * 
     * Logic:
     * 1. Data Integrity: Automatic validation via StoreCasePlanGoalRequest.
     * 2. Persistence: The service handles creation and triggers global cache invalidation.
     *
     * @param StoreCasePlanGoalRequest $request Validated plan goal data.
     * @return JsonResponse
     */
    public function store(StoreCasePlanGoalRequest $request)
    {
        // 1. Service Logic.
        $casePlanGoal = $this->casePlanGoalService->store($request->validated());

        // 2. Response (HTTP 201 Created)
        return self::successResponse('Case plan goal created successfully', $casePlanGoal, 201);
    }

    /**
     * Display the specified case plan goal.
     *
     * ARCHITECTURAL NOTE:
     * We use `int $id` here instead of Route Model Binding (`CasePlanGoal $casePlanGoal`).
     *
     * Why?
     * If we injected `CasePlanGoal $casePlanGoal`, Laravel would execute a DB Query immediately
     * to find the model. This defeats the purpose of our Service Cache.
     *
     * By passing the ID, we let `$this->casePlanGoalService->getById($id)` check the
     * Cache (Redis/File) first. If found, NO database query runs.
     *
     * @param int $id The ID of the case plan goal.
     * @return JsonResponse
     */
    public function show($id)
    {
        // 1. Retrieve Data: Handled by service with "Dual-Tag" caching strategy.
        $casePlanGoal = $this->casePlanGoalService->getById($id);

        // 2. Response
        return self::successResponse('Case plan goal fetched successfully', $casePlanGoal);
    }

    /**
     * Update the specified case support plan in storage.
     *
     * Note: Route Model Binding is used here as we need to verify the resource's 
     * existence and state before performing a destructive update operation.
     *
     * @param UpdateCasePlanGoalRequest $request Validated update data.
     * @param CasePlanGoal $caseSupportPlan Injected model instance.
     * @return JsonResponse
     */
    public function update(UpdateCasePlanGoalRequest $request, CasePlanGoal $casePlanGoal)
    {
        // 1. Service Logic: Updates the record and purges specific cache tags.
        $casePlanGoal = $this->casePlanGoalService->update($casePlanGoal, $request->validated());

        // 2. Response
        return self::successResponse('Case plan goal created successfully', $casePlanGoal);
    }

    /**
     * Remove the specified case support plan from storage.
     *
     * @param CasePlanGoal $caseSupportPlan Injected model instance.
     * @return JsonResponse
     */
    public function destroy(CasePlanGoal $casePlanGoal)
    {
        // 1. Service Logic
        $this->casePlanGoalService->delete($casePlanGoal);

        // 2. Response
        return self::successResponse('Case plan goal deleted successfully');
    }
}
