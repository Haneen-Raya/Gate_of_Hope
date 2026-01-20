<?php

namespace Modules\Assessments\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\IssueCategory;

class IssueCategoryService
{
    private const CACHE_TAGS = ['assessment', 'issue_categories'];
    private const CACHE_KEY_ACTIVE = 'assessment.issue_categories.active';
    private const CACHE_TTL = 21600; // 6 ساعات

    /**
     * Get all active categories (cached)
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
     */
    public function getPaginated($perPage = 15)
    {
        return IssueCategory::orderBy('id')->paginate($perPage);
    }

    /**
     * Create new category
     */
    public function create(array $data): IssueCategory
    {
        $category = IssueCategory::create($data);
        $this->clearCache();
        return $category;
    }

    /**
     * Update existing category
     */
    public function update(IssueCategory $category, array $data): IssueCategory
    {
        $category->update($data);
        $this->clearCache();
        return $category;
    }

    /**
     * Soft delete category and cascade to types
     */
    public function delete(IssueCategory $category): void
    {
        $category->delete(); // Soft delete
        $category->issueTypes()->delete(); // Cascade soft delete
        $this->clearCache();
    }

    /**
     * Restore category and related types
     */
    public function restore(IssueCategory $category): void
    {
        $category->restore();
        $category->issueTypes()->withTrashed()->restore();
        $this->clearCache();
    }

    /**
     * Clear all related cache
     */
    private function clearCache(): void
    {
        Cache::tags(self::CACHE_TAGS)->flush();
        Cache::tags(['assessment', 'issue_types'])->flush();
    }
}
