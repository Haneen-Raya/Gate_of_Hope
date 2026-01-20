<?php

namespace Modules\Assessments\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Assessments\Http\Requests\StoreIssueCategoryRequest;
use Modules\Assessments\Http\Requests\UpdateIssueCategoryRequest;
use Modules\Assessments\Models\IssueCategory;
use Modules\Assessments\Services\IssueCategoryService;

class IssueCategoryController extends Controller
{
    private IssueCategoryService $service;

    public function __construct(IssueCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a paginated list of categories
     */
    public function index()
    {
        $this->authorize('viewAny', IssueCategory::class);

        return response()->json(
            $this->service->getPaginated()
        );
    }

    /**
     * Display all active categories (for dropdowns etc)
     */
    public function active()
    {
        $this->authorize('viewAny', IssueCategory::class);

        return response()->json(
            $this->service->getActive()
        );
    }

    /**
     * Store a newly created category
     */
    public function store(StoreIssueCategoryRequest $request)
    {
        $this->authorize('create', IssueCategory::class);

        return response()->json(
            $this->service->create($request->validated()),
            201
        );
    }

    /**
     * Show a single category
     */
    public function show(IssueCategory $issueCategory)
    {
        $this->authorize('view', $issueCategory);

        return response()->json($issueCategory);
    }

    /**
     * Update an existing category
     */
    public function update(UpdateIssueCategoryRequest $request, IssueCategory $issueCategory)
    {
        $this->authorize('update', $issueCategory);

        return response()->json(
            $this->service->update($issueCategory, $request->validated())
        );
    }

    /**
     * Soft delete a category
     */
    public function destroy(IssueCategory $issueCategory)
    {
        $this->authorize('delete', $issueCategory);

        $this->service->delete($issueCategory);

        return response()->json([
            'message' => 'Category and related types deleted successfully'
        ]);
    }

    /**
     * Restore a soft-deleted category
     */
    public function restore($id)
    {
        $category = IssueCategory::withTrashed()->findOrFail($id);

        $this->authorize('update', $category);

        $this->service->restore($category);

        return response()->json([
            'message' => 'Category and related types restored successfully',
            'category' => $category
        ]);
    }
    //archieve function 
}
