<?php

namespace Modules\HumanResources\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\HumanResources\Enums\Gender;

class TrainerBuilder extends Builder
{
    /**
     * Filter internal trainers
     */
    public function internal(): self
    {
        return $this->where('is_external', false);
    }

    /**
     * Filter external trainers
     */
    public function external(): self
    {
        return $this->where('is_external', true);
    }

    /**
     * Filter trainers by gender
     */
    public function gender(Gender|string $gender): self
    {
        return $this->where(
            'gender',
            $gender instanceof Gender ? $gender->value : $gender
        );
    }

    /**
     * Filter trainers by profession
     */
    public function profession(int $professionId): self
    {
        return $this->where('profession_id', $professionId);
    }

    /**
     * Trainers that have activity sessions
     */
    public function withSessions(): self
    {
        return $this->whereHas('activitySessions');
    }

    /**
     * Trainers without any sessions
     */
    public function withoutSessions(): self
    {
        return $this->whereDoesntHave('activitySessions');
    }

    /**
     * Order by hourly rate
     */
    public function orderByRate(string $direction = 'asc'): self
    {
        return $this->orderBy('hourly_rate', $direction);
    }
}
