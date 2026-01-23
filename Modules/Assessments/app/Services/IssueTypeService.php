<?php

namespace Modules\Assessments\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\IssueType;

/**
 * Service class for managing Issue Types
 *
 * Provides methods to:
 *  - Retrieve all or active issue types (optionally by category, cached)
 *  - Paginate issue types
 *  - Create, update, delete, restore, and deactivate issue types
 *  - Manage caching for performance optimization
 */
class IssueTypeService
{
    private const CACHE_TAGS = ['assessment', 'issue_types'];
    private const CACHE_TTL = 21600; // 6 ساعات

    /**
     * Get all active issue types, optionally filtered by category
     *
     * @param int|null $categoryId Optional category ID to filter types
     * @return \Illuminate\Support\Collection
     */
    public function getAll(?int $categoryId = null)
    {
        $cacheKey = $categoryId
            ? "assessment.issue_types.by_category.$categoryId"
            : 'assessment.issue_types.all';

        return Cache::tags(self::CACHE_TAGS)->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => IssueType::with('issueCategory')
                    ->when($categoryId, fn($q) => $q->where('issue_category_id', $categoryId))
                    ->where('is_active', true)
                    ->get()
        );
    }

    /**
     * Get paginated issue types, optionally filtered by category
     *
     * @param int|null $categoryId Optional category ID to filter types
     * @param int $perPage Number of items per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(?int $categoryId = null, int $perPage = 15)
    {
        return IssueType::with('issueCategory')
            ->when($categoryId, fn($q) => $q->where('issue_category_id', $categoryId))
            ->paginate($perPage);
    }

    /**
     * Create a new issue type
     *
     * @param array $data Type data
     * @return IssueType Newly created issue type
     */
    public function create(array $data): IssueType
    {
        $type = IssueType::create($data);
        $this->clearCache($type);
        return $type;
    }

    /**
     * Update an existing issue type
     *
     * @param IssueType $type
     * @param array $data Updated type data
     * @return IssueType Updated issue type
     */
    public function update(IssueType $type, array $data): IssueType
    {
        $type->update($data);
        $this->clearCache($type);
        return $type;
    }

    /**
     * Soft delete an issue type
     *
     * @param IssueType $type
     * @return void
     */
    public function delete(IssueType $type): void
    {
        $type->delete(); // Soft delete
        $this->clearCache($type);
    }

    /**
     * Restore a soft-deleted issue type
     *
     * @param IssueType $type
     * @return void
     */
    public function restore(IssueType $type): void
    {
        $type->restore();
        $this->clearCache($type);
    }

    /**
     * Deactivate an issue type (alternative to soft delete)
     *
     * @param IssueType $type
     * @return void
     */
    public function deactivate(IssueType $type): void
    {
        $type->update(['is_active' => false]);
        $this->clearCache($type);
    }

    /**
     * Clear cache for all types or by category
     *
     * Ensures that after create/update/delete/restore/deactivate operations,
     * cached data is cleared for consistency.
     *
     * @param IssueType|null $type Optional type to clear category-specific cache
     * @return void
     */
    private function clearCache(?IssueType $type = null): void
    {
        Cache::tags(self::CACHE_TAGS)->flush();

        // Clear category-specific cache if type is provided
        if ($type) {
            Cache::forget("assessment.issue_types.by_category.{$type->issue_category_id}");
        }
    }
}
