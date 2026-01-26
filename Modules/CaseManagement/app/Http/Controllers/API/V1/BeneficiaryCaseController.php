<?php

namespace Modules\CaseManagement\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Modules\CaseManagement\Services\BeneficiaryCaseService;
use Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase\StoreCaseRequest;
use Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase\UpdateCaseRequest;

/**
 * Class BeneficiaryCaseController
 *
 * Handles API requests for the Beneficiary Case Management lifecycle.
 * Utilizes BeneficiaryCaseService for business logic and caching.
 *
 * @package Modules\CaseManagement\Http\Controllers\Api\V1
 */
class BeneficiaryCaseController extends Controller
{
    /**
     * @param BeneficiaryCaseService $caseService
     */
    public function __construct(protected BeneficiaryCaseService $caseService) {}

    /**
     * List all beneficiary cases with dynamic filtering and pagination.
     *
     * @query_param beneficiary_id int Filter by beneficiary.
     * @query_param case_manager_id int Filter by assigned manager.
     * @query_param status string Filter by CaseStatus (e.g., open, closed).
     * @query_param priority string Filter by priority (e.g., High, Low).
     * @query_param region_id int Filter by geographical region.
     * @query_param opened_from date Start range for opening date (YYYY-MM-DD).
     * @query_param opened_to date End range for opening date (YYYY-MM-DD).
     * @query_param beneficiary_columns array|string Columns to select from beneficiary table.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // تم تمرير request()->all() لتمكين الـ Builder من تطبيق الفلاتر تلقائياً
        $cases = $this->caseService->list(request()->all());
        return $this->successResponse('Cases retrieved successfully.', $cases);
    }

    /**
     * Open a new beneficiary case.
     *
     * @param StoreCaseRequest $request
     * @return JsonResponse
     */
    public function store(StoreCaseRequest $request): JsonResponse
    {
        $case = $this->caseService->createCase($request->validated());
        return $this->successResponse('Case opened successfully.', $case, 201);
    }

    /**
     * Retrieve detailed information for a specific case.
     * Includes relationships: Beneficiary (with User name), Manager, Region, and IssueType.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $case = $this->caseService->getCaseById($id);
        return $this->successResponse('Case details retrieved.', $case);
    }

    /**
     * Update an existing case record.
     * Triggers cache invalidation and activity logging.
     *
     * @param UpdateCaseRequest $request
     * @param BeneficiaryCase $case
     * @return JsonResponse
     */
    public function update(UpdateCaseRequest $request, BeneficiaryCase $case): JsonResponse
    {
        $updatedCase = $this->caseService->updateCase($case, $request->validated());
        return $this->successResponse('Case updated successfully.', $updatedCase);
    }

    /**
     * Remove a case from the system.
     * Supports soft deletion if configured in the model.
     *
     * @param BeneficiaryCase $case
     * @return JsonResponse
     */
    public function destroy(BeneficiaryCase $case): JsonResponse
    {
        $this->caseService->deleteCase($case);
        return $this->successResponse('Case deleted successfully.');
    }
}
