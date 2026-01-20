<?php

namespace Modules\Assessments\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\IssueType;

class IssueTypeService
{
    private const CACHE_TAGS = ['assessment', 'issue_types'];
    private const CACHE_TTL = 21600; // 6 ساعات

    /**
     * Get all issue types (optionally filtered by category)
     */
    public function getAll(?int $categoryId = null)
    {
        $cacheKey = $categoryId
            ? "assessment.issue_types.by_category.$categoryId"
            : 'assessment.issue_types.all';

        return Cache::tags(self::CACHE_TAGS)->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => IssueType::with('category')
                    ->when($categoryId, fn($q) => $q->where('issue_category_id', $categoryId))
                    ->where('is_active', true)
                    ->get()
        );
    }

    /**
     * Paginated list
     */
    public function getPaginated(?int $categoryId = null, int $perPage = 15)
    {
        return IssueType::with('category')
            ->when($categoryId, fn($q) => $q->where('issue_category_id', $categoryId))
            ->paginate($perPage);
    }

    /**
     * Create new type
     */
    public function create(array $data): IssueType
    {
        $type = IssueType::create($data);
        $this->clearCache($type);
        return $type;
    }

    /**
     * Update existing type
     */
    public function update(IssueType $type, array $data): IssueType
    {
        $type->update($data);
        $this->clearCache($type);
        return $type;
    }

    /**
     * Soft delete type
     */
    public function delete(IssueType $type): void
    {
        $type->delete(); // Soft delete
        $this->clearCache($type);
    }

    /**
     * Restore soft deleted type
     */
    public function restore(IssueType $type): void
    {
        $type->restore();
        $this->clearCache($type);
    }

    /**
     * Deactivate type (alternative to soft delete)
     */
    public function deactivate(IssueType $type): void
    {
        $type->update(['is_active' => false]);
        $this->clearCache($type);
    }

    /**
     * Clear cache for all types or by category
     */
    private function clearCache(?IssueType $type = null): void
    {
        Cache::tags(self::CACHE_TAGS)->flush();

        // clear category-specific cache
        if ($type) {
            Cache::forget("assessment.issue_types.by_category.{$type->issue_category_id}");
        }
    }
}
    