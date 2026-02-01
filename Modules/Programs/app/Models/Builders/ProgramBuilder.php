<?php

namespace Modules\Programs\Models\Builders;

use Illuminate\Database\Eloquent\Builder;


class ProgramBuilder extends Builder
{
    /**
     * Filter programs by name.
     */
    public function whereNameLike(string $name): self
    {
        return $this->where('name', 'like', "%{$name}%");
    }

    /**
     * Filter by status.
     */
    public function whereStatus(string $status): self
    {
        return $this->where('status', $status);
    }

    /**
     * Filter by Budget Range.
     */
    public function whereBudgetBetween(float $min, float $max): self
    {
        return $this->whereBetween('budget', [$min, $max]);
    }

    /**
     * Filter by Category.
     */
    public function whereCategory(int $categoryId): self
    {
        return $this->where('issue_category_id', $categoryId);
    }
}
