<?php

namespace Modules\Assessments\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Assessments\Http\Requests\IssueCategory\StoreIssueCategoryRequest;
use Modules\Assessments\Http\Requests\IssueCategory\UpdateIssueCategoryRequest;
use Modules\Assessments\Models\IssueCategory;
use Modules\Assessments\Services\IssueCategoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IssueCategoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Service layer responsible for issue category business logic
     */
    private IssueCategoryService $service;

    /**
     * Inject IssueCategoryService
     */
    public function __construct(IssueCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Get paginated list of issue categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $this->authorize('viewAny', IssueCategory::class);

        return $this->successResponse(
            'Categories fetched successfully',
            $this->service->getPaginated()
        );
    }

    /**
     * Get all active issue categories
     * (Used for dropdowns and selectors)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active()
    {
        $this->authorize('viewAny', IssueCategory::class);

        return $this->successResponse(
            'Active categories fetched successfully',
            $this->service->getActive()
        );
    }

    /**
     * Store a newly created issue category
     *
     * @param StoreIssueCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreIssueCategoryRequest $request)
    {
        $this->authorize('create', IssueCategory::class);
        $category = $this->service->create($request->validated());

        return $this->successResponse(
            'Category created successfully',
            $category,
            201
        );
    }

    /**
     * Show a single issue category
     *
     * @param IssueCategory $issueCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(IssueCategory $issueCategory)
    {
        $this->authorize('view', $issueCategory);

        return $this->successResponse(
            'Category fetched successfully',
            $issueCategory
        );
    }

    /**
     * Update an existing issue category
     *
     * @param UpdateIssueCategoryRequest $request
     * @param IssueCategory $issueCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateIssueCategoryRequest $request, IssueCategory $issueCategory)
    {
        $this->authorize('update', $issueCategory);

        $updated = $this->service->update(
            $issueCategory,
            $request->validated()
        );

        return $this->successResponse(
            'Category updated successfully',
            $updated
        );
    }

    /**
     * Soft delete an issue category
     * (Related issue types will also be affected)
     *
     * @param IssueCategory $issueCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(IssueCategory $issueCategory)
    {
        $this->authorize('delete', $issueCategory);

        $this->service->delete($issueCategory);

        return $this->successResponse(
            'Category and related types deleted successfully'
        );
    }

    /**
     * Restore a soft deleted issue category
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(int $id)
    {
        $category = IssueCategory::withTrashed()->findOrFail($id);

        $this->authorize('update', $category);

        $this->service->restore($category);

        return $this->successResponse(
            'Category and related types restored successfully',
            $category
        );
    }
}
