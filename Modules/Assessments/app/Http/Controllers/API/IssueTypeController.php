<?php

namespace Modules\Assessments\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\Assessments\Http\Requests\IssueType\StoreIssueTypeRequest;
use Modules\Assessments\Http\Requests\IssueType\UpdateIssueTypeRequest;
use Modules\Assessments\Models\IssueType;
use Modules\Assessments\Services\IssueTypeService;

class IssueTypeController extends Controller
{
    use AuthorizesRequests;

    private IssueTypeService $service;

    public function __construct(IssueTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display paginated list of issue types
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', IssueType::class);

        $data = $this->service->getPaginated($request->issue_category_id);

        return $this->successResponse('Issue types retrieved successfully', $data);
    }

    /**
     * Display all active types (for dropdowns)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function active(Request $request)
    {
        $this->authorize('viewAny', IssueType::class);

        $data = $this->service->getAll($request->issue_category_id);

        return $this->successResponse('Active issue types retrieved successfully', $data);
    }

    /**
     * Store a newly created issue type
     *
     * @param StoreIssueTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreIssueTypeRequest $request)
    {
        $this->authorize('create', IssueType::class);

        $type = $this->service->create($request->validated());

        return $this->successResponse('Issue type created successfully', $type, 201);
    }

    /**
     * Show single type
     *
     * @param IssueType $issueType
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(IssueType $issueType)
    {
        $this->authorize('view', $issueType);

        return $this->successResponse('Issue type retrieved successfully', $issueType);
    }

    /**
     * Update type
     *
     * @param UpdateIssueTypeRequest $request
     * @param IssueType $issueType
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateIssueTypeRequest $request, IssueType $issueType)
    {
        $this->authorize('update', $issueType);

        $updated = $this->service->update($issueType, $request->validated());

        return $this->successResponse('Issue type updated successfully', $updated);
    }

    /**
     * Soft delete / deactivate type
     *
     * @param IssueType $issueType
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(IssueType $issueType)
    {
        $this->authorize('delete', $issueType);

        $this->service->delete($issueType);

        return $this->successResponse('Issue type deleted successfully');
    }

    /**
     * Restore soft deleted type
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $type = IssueType::withTrashed()->findOrFail($id);

        $this->authorize('update', $type);

        $this->service->restore($type);

        return $this->successResponse('Issue type restored successfully', $type);
    }

    /**
     * Deactivate type (alternative)
     *
     * @param IssueType $issueType
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(IssueType $issueType)
    {
        $this->authorize('update', $issueType);

        $this->service->deactivate($issueType);

        return $this->successResponse('Issue type deactivated');
    }
}
