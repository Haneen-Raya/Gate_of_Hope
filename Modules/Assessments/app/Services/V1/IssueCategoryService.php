<?php

namespace Modules\Assessments\Services\V1;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\IssueCategory;

/**
 * Service class for managing Issue Categories
 *
 * - Uses Spatie Translatable (DB-based translations)
 * - Caches active categories per locale
 * - Handles create, update, delete, restore
 */
class IssueCategoryService
{
    private const CACHE_TAGS = ['assessment', 'issue_categories'];
    private const CACHE_KEY_ACTIVE = 'assessment.issue_categories.active';
    private const CACHE_TTL = 21600; // 6 hours

    /**
     * Generate cache key per locale
     */
    private function activeCacheKey(): string
    {
        return self::CACHE_KEY_ACTIVE . '.' . app()->getLocale();
    }

    /**
     * Get all active categories (cached per locale)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActive()
    {
        return Cache::tags(self::CACHE_TAGS)->remember(
            $this->activeCacheKey(),
            self::CACHE_TTL,
            fn () => IssueCategory::where('is_active', true)
                ->orderBy('id')
                ->get()
        );
    }

    /**
     * Get paginated categories
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15)
    {
        return IssueCategory::orderBy('id')->paginate($perPage);
    }

    /**
     * Create a new category
     *
     * @param array $data
     * @return IssueCategory
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
     * @param array $data
     * @return IssueCategory
     */
    public function update(IssueCategory $category, array $data): IssueCategory
    {
        $category->update($data);
        $this->clearCache();

        return $category;
    }

    /**
     * Soft delete a category
     * (IssueTypes will be soft deleted via model booted)
     *
     * @param IssueCategory $category
     */
    public function delete(IssueCategory $category): void
    {
        $category->delete();
        $this->clearCache();
    }

    /**
     * Restore a soft-deleted category
     * (IssueTypes will be restored via model booted)
     *
     * @param IssueCategory $category
     */
    public function restore(IssueCategory $category): void
    {
        $category->restore();
        $this->clearCache();
    }

    /**
     * Clear related caches
     */
    private function clearCache(): void
    {
        Cache::tags(self::CACHE_TAGS)->flush();
        Cache::tags(['assessment', 'issue_types'])->flush();
    }
}
