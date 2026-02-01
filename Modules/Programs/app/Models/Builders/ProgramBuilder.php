<?php

namespace Modules\Programs\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProgramBuilder
 * * Custom Query Builder for the Program model.
 * Provides a fluent API for filtering programs based on domain-specific criteria.
 * Using a dedicated builder ensures the "Skinny Controller, Fat Model" principle
 * while keeping complex queries reusable and unit-testable.
 * * @package Modules\Programs\Models\Builders
 * @extends Builder<\Modules\Programs\Models\Program>
 */
class ProgramBuilder extends Builder
{
    /**
     * Filter programs by name using a partial match.
     * * @param string $name The search term for the program name.
     * @return $this
     */
    public function whereNameLike(string $name): self
    {
        return $this->where('name', 'like', "%{$name}%");
    }

    /**
     * Filter programs by their current lifecycle status.
     * * @param string $status The status value (e.g., active, draft, completed).
     * @return $this
     */
    public function whereStatus(string $status): self
    {
        return $this->where('status', $status);
    }

    /**
     * Filter programs within a specific financial budget range.
     * * @param float $min Minimum budget threshold.
     * @param float $max Maximum budget threshold.
     * @return $this
     */
    public function whereBudgetBetween(float $min, float $max): self
    {
        return $this->whereBetween('budget', [$min, $max]);
    }

    /**
     * Filter programs associated with a specific issue category.
     * * @param int $categoryId The foreign key ID of the issue category.
     * @return $this
     */
    public function whereCategory(int $categoryId): self
    {
        return $this->where('issue_category_id', $categoryId);
    }
}
