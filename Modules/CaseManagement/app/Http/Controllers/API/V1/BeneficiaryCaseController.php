<?php

namespace Modules\CaseManagement\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\CaseManagement\Services\BeneficiaryCaseService;
use Modules\CaseManagement\Services\BeneficiaryReportService;
use Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase\StoreCaseRequest;
use Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase\UpdateCaseRequest;

/**
 * Class BeneficiaryCaseController
 * * @package Modules\CaseManagement\Http\Controllers\Api\V1
 * @author Case Management Team
 * @version 1.2.0
 * * ARCHITECTURAL ROLE:
 * This controller serves as the primary API Gateway for managing the lifecycle of Beneficiary Cases.
 * It adheres to the 'Thin Controller' pattern by delegating complex business logic,
 * cross-module data aggregation, and performance caching to specialized Service Layers.
 * * SECURITY:
 * Implements granular Policy-based Authorization (Laravel Gate) for every endpoint to ensure
 * data integrity and multi-tenant security isolation.
 */
class BeneficiaryCaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Dependency Injection Constructor.
     * * @param BeneficiaryCaseService $caseService Orchestrates core CRUD operations and cache invalidation.
     * @param BeneficiaryReportService $reportService Handles high-complexity analytical report generation.
     */
    public function __construct(
        protected BeneficiaryCaseService $caseService,
        protected BeneficiaryReportService $reportService
    ) {}

    /**
     * Display a paginated collection of beneficiary cases.
     * * Supports dynamic multi-column filtering, date range constraints, and relationship
     * selective loading through the CaseService's advanced query builder.
     * * @query_param beneficiary_id int|null Optional filter by beneficiary entity.
     * @query_param status string|null Filter by operational status (open, closed, etc.).
     * @query_param opened_from date|null ISO-8601 start date for range filtering.
     * * @return JsonResponse Standardized API success response with case collection metadata.
     * @throws \Illuminate\Auth\Access\AuthorizationException If current user lacks 'viewAny' permission.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', BeneficiaryCase::class);
        $cases = $this->caseService->list(request()->all());
        return $this->successResponse('Cases retrieved successfully.', $cases);
    }

    /**
     * Initialize and persist a new beneficiary case.
     * * This method triggers a transactional sequence: Validation -> Policy Check ->
     * Database Persistence -> Activity Logging.
     * * @param StoreCaseRequest $request Validated request object containing Case attributes.
     * @return JsonResponse 201 Created response containing the newly generated resource.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreCaseRequest $request): JsonResponse
    {
        $this->authorize('create', BeneficiaryCase::class);
        $case = $this->caseService->createCase($request->validated());
        return $this->successResponse('Case opened successfully.', $case, 201);
    }

    /**
     * Fetch a singular case record with comprehensive relationship hydration.
     * * Retrieves the case along with Beneficiary profile, Assigned Manager,
     * Geographical Region, and specific Issue Classifications.
     * * @param int $id Primary key of the BeneficiaryCase resource.
     * @return JsonResponse Detailed resource payload.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the ID is invalid.
     */
    public function show(int $id): JsonResponse
    {
        $case = $this->caseService->getCaseById($id);
        $this->authorize('view', $case);
        return $this->successResponse('Case details retrieved.', $case);
    }

    /**
     * Update an existing case entity.
     * * Updates specified attributes and automatically invalidates associated
     * cache tags to maintain data consistency across the cluster.
     * * @param UpdateCaseRequest $request
     * @param BeneficiaryCase $case Route-model bound instance of the case.
     * @return JsonResponse Updated resource payload.
     */
    public function update(UpdateCaseRequest $request, BeneficiaryCase $case): JsonResponse
    {
        $this->authorize('update', $case);
        $updatedCase = $this->caseService->updateCase($case, $request->validated());
        return $this->successResponse('Case updated successfully.', $updatedCase);
    }

    /**
     * Terminate/Remove a case from the operational layer.
     * * Implements safe deletion logic. If SoftDeletes are enabled in the model,
     * it archives the record; otherwise, it performs a permanent purge.
     * * @param BeneficiaryCase $case
     * @return JsonResponse Success acknowledgement without data payload.
     */
    public function destroy(BeneficiaryCase $case): JsonResponse
    {
        $this->authorize('delete', $case);
        $this->caseService->deleteCase($case);
        return $this->successResponse('Case deleted successfully.');
    }

    /**
     * Generate a Professional Analytical Report for a specific case.
     * * Aggregates milestones, clinical review history, and chronological event
     * timelines into a formatted document-ready JSON structure.
     * * @param int $id The unique identifier of the case.
     * @return JsonResponse Holistic analytical report structure.
     * @see BeneficiaryReportService::generateProfessionalReport()
     */
    public function getReport($id): JsonResponse
    {
        $reportData = $this->reportService->generateProfessionalReport($id);

        return $this->successResponse(
            'Beneficiary progress report generated successfully.',
            $reportData
        );
    }
}
