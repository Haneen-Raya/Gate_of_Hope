<?php

namespace Modules\Beneficiaries\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class EmploymentStatusBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters and search conditions on the
 * EmploymentStatus model.
 *
 * This builder provides a clean and fluent interface
 * to filter employment status based on request inputs.
 *
 * @package Modules\Beneficiaries\Models\Builders
 *
 * @method self searchByName(?string $term)
 * @method self filter(array $filters)
 */
class EmploymentStatusBuilder extends Builder
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
     * Search employment statuses by name.
     *
     * Applies a LIKE query on the name column
     * if the search term is provided.
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
     * Apply dynamic filters on employment statuses.
     *
     * This is the main entry point for applying multiple filters
     * based on request parameters.
     *
     * Supported filters:
     * - is_active : bool|null
     * - name      : string|null
     *
     * @param array<string, mixed> $filters
     *
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            ->handleGlobalScopeBypass($filters['is_active'] ?? null)
            ->searchByName($filters['name'] ?? null);
    }
}
