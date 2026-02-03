<?php

namespace Modules\HumanResources\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\HumanResources\Enums\Gender;

/**
 * Class TrainerBuilder
 * * Orchestrates complex Eloquent queries for the Trainer model.
 * Provides a fluent interface for workforce analysis, specialized in filtering
 * by origin (Internal/External), demographics, and session engagement.
 */
class TrainerBuilder extends Builder
{
    /**
     * Scope query to internal trainers (staff).
     */
    public function internal(): self
    {
        return $this->where('is_external', false);
    }

    /**
     * Scope query to external consultants/contractors.
     */
    public function external(): self
    {
        return $this->where('is_external', true);
    }

    /**
     * Filter trainers by gender using Type-Safe Enums.
     * * @param Gender|string $gender
     */
    public function gender(Gender|string $gender): self
    {
        return $this->where(
            'gender',
            $gender instanceof Gender ? $gender->value : $gender
        );
    }

    /**
     * Filter by professional classification ID.
     */
    public function profession(int $professionId): self
    {
        return $this->where('profession_id', $professionId);
    }

    /**
     * Retrieve trainers with at least one recorded activity session.
     */
    public function withSessions(): self
    {
        return $this->whereHas('activitySessions');
    }

    /**
     * Retrieve idle trainers without any assigned sessions.
     */
    public function withoutSessions(): self
    {
        return $this->whereDoesntHave('activitySessions');
    }

    /**
     * Apply financial ordering by hourly compensation.
     * * @param string $direction 'asc' or 'desc'.
     */
    public function orderByRate(string $direction = 'asc'): self
    {
        return $this->orderBy('hourly_rate', $direction);
    }
}
