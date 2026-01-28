<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ServiceBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters and search conditions on the
 * Service model.
 *
 * This builder provides a fluent interface to filter
 * services based on request parameters such as:
 * - activation status
 * - direction
 * - issue category
 * - unit cost range
 * - name search
 *
 * @package Modules\CaseManagement\Models\Builders
 *
 * @method self filterDirection(?string $direction)
 * @method self filterIssueCategory(?int $issueCategoryId)
 * @method self filterUnitCost(?float $min, ?float $max)
 * @method self searchByName(?string $term)
 * @method self filter(array $filters)
 */
class ServiceBuilder extends Builder
{
    /**
     * Handle bypassing the global active scope.
     *
     * If the user passes is_active = false,
     * the builder will explicitly return only inactive records.
     *
     * @param mixed $isActiveValue
     *      Value passed from filters (true/false/null).
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
     * Filter services by direction.
     *
     * This filter matches the direction column exactly.
     *
     * @param string|null $direction
     *
     * @return self
     */
    public function filterDirection(?string $direction): self
    {
        return $this->when($direction, fn($q) => $q->where('direction', $direction));

    }

    /**
     * Filter services by issue category.
     *
     * Applies filtering using the foreign key issue_category_id.
     *
     * @param int|null $issueCategoryId
     *
     * @return self
     */
    public function filterIssueCategory(?int $issueCategoryId): self
    {
        return $this->when($issueCategoryId, fn($q) => $q->where('issue_category_id', $issueCategoryId));
    }

    /**
     * Search services by name.
     *
     * Applies a LIKE query on the name column if a term is provided.
     *
     * @param string|null $term
     *
     * @return self
     */
    public function searchByName(?string $term): self
    {
        return $this->when($term, fn($q) => $q->where('name', 'like', "%{$term}%"));
    }

    /**
     * Filter services by unit cost range.
     *
     * Allows filtering between minimum and maximum cost values.
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
     * Apply dynamic filters on services.
     *
     * This is the main entry point for applying multiple filters
     * based on request parameters.
     *
     * Supported filters:
     * - is_active          : bool|null
     * - direction          : string|null
     * - issue_category_id  : int|null
     * - min_cost           : float|null
     * - max_cost           : float|null
     * - name               : string|null
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
