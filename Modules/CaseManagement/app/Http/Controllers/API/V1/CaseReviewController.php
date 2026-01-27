<?php

namespace Modules\CaseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseReview\StoreCaseReviewRequest;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseReview\UpdateCaseReviewRequest;
use Modules\CaseManagement\Models\CaseReview;
use Modules\CaseManagement\Services\CaseReviewService;
use Symfony\Component\HttpFoundation\JsonResponse;

class CaseReviewController extends Controller
{

    /**
     * Service to handle case-review-related logic 
     * and separating it from the controller
     * 
     * @var CaseReviewService
     */
    protected $caseReviewService;

    /**
     * CaseReviewController constructor
     *
     * @param CaseReviewService $caseReviewService
     */
    public function __construct(CaseReviewService $caseReviewService)
    {
        // Inject the CaseReviewService to handle case-review-related logic
        $this->caseReviewService = $caseReviewService;
    }

    /**
     * Display a paginated listing of Case Reviews based on dynamic criteria.
     * 
     * * * Orchestration Workflow:
     * 1. **Input Extraction:** Captures all raw query parameters from the request payload 
     * to support flexible filtering (e.g., case_id, progress_status, date ranges).
     * 2. **Service Execution:** Delegates the business logic and high-performance caching 
     * retrieval to the `CaseReviewService`.
     * 3. **Standardized Response:** Wraps the paginated result in a consistent JSON structure, 
     * ensuring a predictable contract for Frontend consumers.
     *
     * @param Request $request Encapsulates filter parameters and pagination state.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response with Pagination metadata.
     */
    public function index(Request $request)
    {
        // 1. Filter Extraction: Gather all dynamic filter criteria from the request.
        $filters = $request->all();

        // 2. Execution: Fetch cached and filtered data through the service layer.
        $caseReviews = $this->caseReviewService->list($filters);

        // 3. Response
        return self::successResponse('Case Reviews fetched successfully', $caseReviews);
    }

    /**
     * Store a newly created Case Review in the persistent storage.
     * 
     * * Execution Lifecycle:
     * 1. **Request Validation:** Implicitly utilizes `StoreCaseReviewRequest` to enforce 
     * strict data integrity and business rules before execution.
     * 2. **Validated Payload:** Passes only the sanitized data via `$request->validated()` 
     * to the service layer, preventing mass-assignment vulnerabilities.
     * 3. **Service Orchestration:** Delegates the specialist binding and database 
     * persistence logic to the `CaseReviewService`.
     * 4. **Success Delivery:** Returns a `201 Created` status code, adhering to 
     * REST standards for successful resource creation.
     *
     * @param StoreCaseReviewRequest $request The pre-validated form request instance.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response with the created resource.
     */
    public function store(StoreCaseReviewRequest $request)
    {
        $caseReview =  $this->caseReviewService->store($request->validated());

        return self::successResponse('Case review created successfully', $caseReview, 201);
    }

    /**
     * Display the specified case review.
     *
     * ARCHITECTURAL NOTE:
     * We use `int $id` here instead of Route Model Binding (`CaseReview $caseReview`).
     *
     * Why?
     * If we injected `CaseReview $caseReview`, Laravel would execute a DB Query immediately
     * to find the model. This defeats the purpose of our Service Cache.
     *
     * By passing the ID, we let `$this->caseReviewService->getById($id)` check the
     * Cache (Redis/File) first. If found, NO database query runs.
     *
     * @param int $id The ID of the case support plan.
     * @return JsonResponse
     */
    public function show($id)
    {
        $caseReview =  $this->caseReviewService->getById($id);

        return self::successResponse('Case review fetched successfully', $caseReview);
    }

    /**
     * Update the specified Case Review in persistent storage.
     * 
     * * Execution Lifecycle:
     * 1. **Resource Identification:** Leverages Route Model Binding to automatically 
     * resolve the `CaseReview` instance, ensuring the target record exists.
     * 2. **Data Sanitization:** Utilizes `UpdateCaseReviewRequest` to filter the 
     * incoming payload, permitting only authorized "dirty" fields to be modified.
     * 3. **Persistence Orchestration:** Delegates the update logic and cache 
     * invalidation triggers to the `CaseReviewService`.
     * 4. **State Refresh:** Delivers the updated and refreshed model instance back 
     * to the consumer to reflect any DB-level changes or updated timestamps.
     *
     * @param UpdateCaseReviewRequest $request The pre-validated update request.
     * @param CaseReview $caseReview The resolved model instance to be modified.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response.
     */
    public function update(UpdateCaseReviewRequest $request, CaseReview $caseReview)
    {
        $caseReview = $this->caseReviewService->update($caseReview, $request->validated());

        return self::successResponse('Case review updated successfully', $caseReview);
    }

    /**
     * Remove the specified case review from storage.
     *
     * @param CaseReview $caseReview Injected model instance.
     * @return JsonResponse
     */
    public function destroy(CaseReview $caseReview)
    {
        $this->caseReviewService->delete($caseReview);

        return self::successResponse('Case review deleted successfully');
    }
}
