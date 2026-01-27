<?php

namespace Modules\CaseManagement\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class ServiceBuilder extends Builder
{
    /**
     * Filter by activation status.
     *
     * @param mixed $isActiveValue
     *
     * @return self
     */
    protected function handleGlobalScopeBypass($isActiveValue): self
    {
        if (isset($isActiveValue) && ! (bool) $isActiveValue) {
            return $this->where('is_active', false);
        }

        return $this;
    }

    /**
     * Filter by service direction.
     *
     * @param string|null $direction
     *
     * @return self
     */
    public function filterDirection(?string $direction): self
    {
        return $this->when($direction, function ($q) use ($direction) {
            $q->where('direction', $direction);
        });
    }

    /**
     * Filter by issue category.
     *
     * @param int|null $issueCategoryId
     *
     * @return self
     */
    public function filterIssueCategory(?int $issueCategoryId): self
    {
        return $this->when($issueCategoryId, function ($q) use ($issueCategoryId) {
            $q->where('issue_category_id', $issueCategoryId);
        });
    }

    /**
     * Search services by name.
     *
     * @param string|null $term
     *
     * @return self
     */
    public function searchByName(?string $term): self
    {
        return $this->when($term, function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%");
        });
    }

    /**
     * Filter by unit cost range.
     *
     * @param float|null $min
     * @param float|null $max
     *
     * @return self
     */
    public function filterUnitCost(?float $min, ?float $max): self
    {
        return $this->when($min !== null, function ($q) use ($min) {
                    $q->where('unit_cost', '>=', $min);
                })
                ->when($max !== null, function ($q) use ($max) {
                    $q->where('unit_cost', '<=', $max);
                });
    }

    /**
     * Entry point to apply dynamic filters.
     *
     * This method provides a clean, fluent interface to conditionally apply
     * various search parameters from a user request.
     * 
     * @param array<string, mixed> $filters
     *
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            ->handleGlobalScopeBypass($filters['is_active'] ?? null)
            ->filterDirection($filters['direction'] ?? null)
            ->filterIssueCategory($filters['issue_category_id'] ?? null)
            ->filterUnitCost($filters['min_cost'] ?? null, $filters['max_cost'] ?? null)
            ->searchByName($filters['name'] ?? null);
    }
}
