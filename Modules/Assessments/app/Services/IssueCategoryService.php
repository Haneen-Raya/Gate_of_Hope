<?php

namespace Modules\Assessments\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\IssueCategory;

/**
 * Service class for managing Issue Categories
 *
 * Provides methods to:
 *  - Retrieve active categories (cached)
 *  - Paginate categories
 *  - Create, update, delete, and restore categories
 *  - Handle cascading operations on related Issue Types
 *  - Manage caching for performance optimization
 */
class IssueCategoryService
{
    private const CACHE_TAGS = ['assessment', 'issue_categories'];
    private const CACHE_KEY_ACTIVE = 'assessment.issue_categories.active';
    private const CACHE_TTL = 21600; // 6 hours

    /**
     * Get all active categories (cached)
     *
     * Retrieves all categories where `is_active` is true,
     * ordered by ID. Uses caching to improve performance.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActive()
    {
        return Cache::tags(self::CACHE_TAGS)->remember(
            self::CACHE_KEY_ACTIVE,
            self::CACHE_TTL,
            fn () => IssueCategory::where('is_active', true)
                ->orderBy('id')
                ->get()
        );
    }

    /**
     * Get paginated categories
     *
     * Retrieves categories in a paginated format ordered by ID.
     *
     * @param int $perPage Number of items per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated($perPage = 15)
    {
        return IssueCategory::orderBy('id')->paginate($perPage);
    }

    /**
     * Create a new category
     *
     * @param array $data Category data
     * @return IssueCategory The newly created category
     */
    public function create(array $data): IssueCategory
    {
        $category = IssueCategory::create($data);
        $this->clearCache();
        return $category;
    }

    /**
     * Update an existing category
     *
     * @param IssueCategory $category
     * @param array $data Updated category data
     * @return IssueCategory The updated category
     */
    public function update(IssueCategory $category, array $data): IssueCategory
    {
        $category->update($data);
        $this->clearCache();
        return $category;
    }

    /**
     * Soft delete a category and cascade to related issue types
     *
     * @param IssueCategory $category
     * @return void
     */
    public function delete(IssueCategory $category): void
    {
        $category->delete(); // Soft delete
        $category->issueTypes()->delete(); // Cascade soft delete
        $this->clearCache();
    }

    /**
     * Restore a soft-deleted category and its related issue types
     *
     * @param IssueCategory $category
     * @return void
     */
    public function restore(IssueCategory $category): void
    {
        $category->restore();
        $category->issueTypes()->withTrashed()->restore();
        $this->clearCache();
    }

    /**
     * Clear all related cache
     *
     * Flushes cached data for categories and issue types
     * to ensure data consistency after create/update/delete/restore operations.
     *
     * @return void
     */
    private function clearCache(): void
    {
        Cache::tags(self::CACHE_TAGS)->flush();
        Cache::tags(['assessment', 'issue_types'])->flush();
    }
}
