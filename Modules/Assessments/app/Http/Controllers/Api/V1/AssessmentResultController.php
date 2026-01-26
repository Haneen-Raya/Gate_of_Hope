<?php

namespace Modules\Assessments\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Assessments\Http\Requests\V1\Assessment\UpdateAssessmentPriorityRequest;
use Modules\Assessments\Models\AssessmentResult;
use Modules\Assessments\Services\V1\AssessmentResultService;
use Symfony\Component\HttpFoundation\JsonResponse;

class AssessmentResultController extends Controller
{

    /**
     * Service to handle assessment-result-related logic 
     * and separating it from the controller
     * 
     * @var AssessmentResultService
     */
    protected $assessmentResultService;

    /**
     * AssessmentResultController constructor
     *
     * @param AssessmentResultService $assessmentResultService
     */
    public function __construct(AssessmentResultService $assessmentResultService)
    {
        // Inject the AssessmentResultService to handle assessment-result logic
        $this->assessmentResultService = $assessmentResultService;
    }

    /**
     * Display a paginated listing of Assessment Results based on analytical criteria.
     * * * * Orchestration Workflow:
     * 1. **Input Extraction:** Aggregates all query parameters from the request payload 
     * to support deep filtering (e.g., beneficiary_id, normalized_score, priority_final).
     * 2. **Analytical Execution:** Delegates the complex filtering and MD5-based 
     * caching logic to the `AssessmentResultService`.
     * 3. **Standardized Response:** Dispatches a structured JSON payload containing 
     * both the assessment data and pagination metadata for the client-side UI.
     *
     * @param Request $request Encapsulates analytical filters and pagination state.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response with paginated results.
     */
    public function index(Request $request)
    {
        // 1. Filter Extraction: Gather all dynamic filter criteria from the request.
        $filters = $request->all();

        // 2. Execution: Fetch cached and filtered data through the service layer.
        $caseSupportPlans = $this->assessmentResultService->list($filters);

        // 3. Response
        return self::successResponse('Assessment results fetched successfully', $caseSupportPlans);
    }

    /**
     * Display the specified Assessment result.
     *
     * ARCHITECTURAL NOTE:
     * We use `int $id` here instead of Route Model Binding (`AssessmentResult $assessmentResult`).
     *
     * Why?
     * If we injected `AssessmentResult $assessmentResult`, Laravel would execute a DB Query immediately
     * to find the model. This defeats the purpose of our Service Cache.
     *
     * By passing the ID, we let `$this->assessmentResultService->getById($id)` check the
     * Cache (Redis/File) first. If found, NO database query runs.
     *
     * @param int $id The ID of the Assessment Result.
     * @return JsonResponse
     */
    public function show($id)
    {
        // 1. Retrieve Data (Cache Hit or DB Query via Service)
        $assessmentResult = $this->assessmentResultService->getById($id);

        // 2. Response
        return self::successResponse('Assessment result fetched successfully', $assessmentResult);
    }

    /**
     * Update the specified Assessment Result in persistent storage.
     *
     * Note: This method utilizes Route Model Binding (`AssessmentResult $assessmentResult`) 
     * to ensure the integrity of the resource. By injecting the instance, we verify 
     * existence and ownership before committing any priority or score modifications.
     *
     * * * Execution Lifecycle:
     * 1. **Data Sanitization:** Processes only the validated fields from `UpdateAssessmentPriorityRequest` 
     * (e.g., priority_final, justification) to prevent unauthorized attribute mutation.
     * 2. **Service Delegation:** Hands over the persistence logic to `AssessmentResultService` 
     * to manage complex state transitions and cache invalidation.
     * 3. **Synchronized Response:** Returns the latest database state, ensuring that any 
     * computed fields or audit timestamps are accurately reflected in the UI.
     *
     * @param UpdateAssessmentPriorityRequest $request Pre-validated payload focusing on priority/justification.
     * @param AssessmentResult $assessmentResult The resolved model instance to be modified.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response with refreshed data.
     */
    public function update(UpdateAssessmentPriorityRequest $request, AssessmentResult $assessmentResult)
    {
        // 1. Service Logic
        $assessmentResult = $this->assessmentResultService->update($assessmentResult, $request->validated());

        // 2. Response
        return self::successResponse('Assessment result updated successfully', $assessmentResult);
    }

    /**
     * Remove the specified assessment result from storage.
     *
     * @param AssessmentResult $assessmentResult Injected model instance.
     * @return JsonResponse
     */
    public function destroy(AssessmentResult $assessmentResult)
    {
        // 1. Service Logic
        $this->assessmentResultService->delete($assessmentResult);

        // 2. Response
        return self::successResponse('Assessment result deleted successfully');
    }
}
