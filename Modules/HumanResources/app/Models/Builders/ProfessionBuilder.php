<?php

namespace Modules\HumanResources\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Custom Query Builder for the Profession Model.
 *
 * This class orchestrates the filtering logic for professional classifications,
 * enabling streamlined management of specialist roles and workforce taxonomies.
 *
 * @extends Builder<\Modules\HumanResources\Models\Profession>
 */
class ProfessionBuilder extends Builder
{
    /**
     * Filter professions by their descriptive name (Fuzzy Search).
     *
     * @param string $name
     * @return self
     */
    public function byName(string $name): self
    {
        return $this->where('name', 'like', "%{$name}%");
    }

    /**
     * Filter professions by their unique system code.
     *
     * @param string $code
     * @return self
     */
    public function byCode(string $code): self
    {
        return $this->where('code', $code);
    }

    /**
     * Orchestrate dynamic query filtering for Professions.
     * * Handles key organizational dimensions:
     * 1. **Identification:** Search by professional nomenclature.
     * 2. **System Mapping:** Exact match filtering via unique codes.
     * 3. **Availability Control:** Filtering by active/inactive status.
     *
     * @param array<string, mixed> $filters {
     * @var string|null $name      Search by profession name (Partial match).
     * @var string|null $code      Exact match by profession unique code.
     * @var bool|null   $is_active Filter by employment availability status.
     * }
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            // ---------------------------------------------------
            // 1. Identification & Nomenclature
            // ---------------------------------------------------
            ->when($filters['name'] ?? null, fn($q, $name) => $q->byName($name))

            // ---------------------------------------------------
            // 2. System Mapping (Exact Match)
            // ---------------------------------------------------
            ->when($filters['code'] ?? null, fn($q, $code) => $q->byCode($code))

            // ---------------------------------------------------
            // 3. Default Ordering
            // ---------------------------------------------------
            ->latest();
    }
}