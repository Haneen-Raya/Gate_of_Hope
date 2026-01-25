<?php

namespace Modules\CaseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseSupportPlan\StoreCaseSupportPlanRequest;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseSupportPlan\UpdateCaseSupportPlanRequest;
use Modules\CaseManagement\Models\CaseSupportPlan;
use Modules\CaseManagement\Services\CaseSupportPlanService;
use Symfony\Component\HttpFoundation\JsonResponse;

class CaseSupportPlanController extends Controller
{
    /**
     * Service to handle case-support-plan-related logic
     * and separating it from the controller
     *
     * @var CaseSupportPlanService
     */
    protected $caseSupportPlanService;

    /**
     * CaseSupportPlanController constructor
     *
     * @param CaseSupportPlanService $caseSupportPlanService
     */
    public function __construct(CaseSupportPlanService $caseSupportPlanService)
    {
        // Inject the CaseSupportPlanService to handle case-support-plan-related logic
        $this->caseSupportPlanService = $caseSupportPlanService;
    }

    /**
     * Display a paginated listing of support plans with dynamic filtering.
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
        $caseSupportPlans = $this->caseSupportPlanService->list($filters);

        // 3. Response
        return self::successResponse('Case Support Plans fetched successfully', $caseSupportPlans);
    }

    /**
     * Store a newly created support plan in storage.
     *
     * Logic:
     * 1. Data Integrity: Automatic validation via StoreCaseSupportPlanRequest.
     * 2. Persistence: The service handles creation and triggers global cache invalidation.
     *
     * @param StoreCaseSupportPlanRequest $request Validated plan data.
     * @return JsonResponse
     */
    public function store(StoreCaseSupportPlanRequest $request)
    {
        // 1. Service Logic: Persists data and manages audit trail (created_by/updated_by).
        $caseSupportPlan = $this->caseSupportPlanService->store($request->validated());

        // 2. Response (HTTP 201 Created)
        return self::successResponse('Case support plan created successfully', $caseSupportPlan, 201);
    }

    /**
     * Display the specified case support plan.
     *
     * ARCHITECTURAL NOTE:
     * We use `int $id` here instead of Route Model Binding (`CaseSupportPlan $caseSupportPlan`).
     *
     * Why?
     * If we injected `CaseSupportPlan $caseSupportPlan`, Laravel would execute a DB Query immediately
     * to find the model. This defeats the purpose of our Service Cache.
     *
     * By passing the ID, we let `$this->beneficiaryService->getById($id)` check the
     * Cache (Redis/File) first. If found, NO database query runs.
     *
     * @param int $id The ID of the case support plan.
     * @return JsonResponse
     */
    public function show(int $id)
    {
        // 1. Retrieve Data: Handled by service with "Dual-Tag" caching strategy.
        $caseSupportPlan = $this->caseSupportPlanService->getById($id);

        // 2. Response
        return self::successResponse('Case support plan fetched successfully', $caseSupportPlan);
    }

    /**
     * Update the specified case support plan in storage.
     *
     * Note: Route Model Binding is used here as we need to verify the resource's
     * existence and state before performing a destructive update operation.
     *
     * @param UpdateCaseSupportPlanRequest $request Validated update data.
     * @param CaseSupportPlan $caseSupportPlan Injected model instance.
     * @return JsonResponse
     */
    public function update(UpdateCaseSupportPlanRequest $request, CaseSupportPlan $caseSupportPlan)
    {
        // 1. Service Logic: Updates the record and purges specific cache tags.
        $caseSupportPlan = $this->caseSupportPlanService->update($caseSupportPlan, $request->validated());

        // 2. Response
        return self::successResponse('Case support plan updated successfully', $caseSupportPlan);
    }

    /**
     * Remove the specified case support plan from storage.
     *
     * @param CaseSupportPlan $caseSupportPlan Injected model instance.
     * @return JsonResponse
     */
    public function destroy(CaseSupportPlan $caseSupportPlan)
    {
        // 1. Service Logic
        $this->caseSupportPlanService->delete($caseSupportPlan);

        // 2. Response
        return self::successResponse('Case support plan deleted successfully');
    }
}
