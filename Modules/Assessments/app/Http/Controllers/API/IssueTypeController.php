<?php

namespace Modules\Assessments\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Assessments\Http\Requests\StoreIssueTypeRequest;
use Modules\Assessments\Http\Requests\UpdateIssueTypeRequest;
use Modules\Assessments\Models\IssueType;
use Modules\Assessments\Services\IssueTypeService;

class IssueTypeController extends Controller
{
    private IssueTypeService $service;

    public function __construct(IssueTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display paginated list of issue types
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', IssueType::class);

        return response()->json(
            $this->service->getPaginated($request->issue_category_id)
        );
    }

    /**
     * Display all active types (for dropdowns)
     */
    public function active(Request $request)
    {
        $this->authorize('viewAny', IssueType::class);

        return response()->json(
            $this->service->getAll($request->issue_category_id)
        );
    }

    /**
     * Store a newly created issue type
     */
    public function store(StoreIssueTypeRequest $request)
    {
        $this->authorize('create', IssueType::class);

        return response()->json(
            $this->service->create($request->validated()),
            201
        );
    }

    /**
     * Show single type
     */
    public function show(IssueType $issueType)
    {
        $this->authorize('view', $issueType);

        return response()->json($issueType);
    }

    /**
     * Update type
     */
    public function update(UpdateIssueTypeRequest $request, IssueType $issueType)
    {
        $this->authorize('update', $issueType);

        return response()->json(
            $this->service->update($issueType, $request->validated())
        );
    }

    /**
     * Soft delete / deactivate type
     */
    public function destroy(IssueType $issueType)
    {
        $this->authorize('delete', $issueType);

        $this->service->delete($issueType);

        return response()->json([
            'message' => 'Issue type deleted successfully'
        ]);
    }

    /**
     * Restore soft deleted type
     */
    public function restore($id)
    {
        $type = IssueType::withTrashed()->findOrFail($id);

        $this->authorize('update', $type);

        $this->service->restore($type);

        return response()->json([
            'message' => 'Issue type restored successfully',
            'type' => $type
        ]);
    }

    /**
     * Deactivate type (alternative)
     */
    public function deactivate(IssueType $issueType)
    {
        $this->authorize('update', $issueType);

        $this->service->deactivate($issueType);

        return response()->json(['message' => 'Issue type deactivated']);
    }
}
