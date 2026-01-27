<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\CaseManagement\Enums\SessionType;

class CaseSessionBuilder extends Builder
{
    /**
     * Filter by beneficiary case
     */
    public function forCase(int $caseId): self
    {
        return $this->where('beneficiary_case_id', $caseId);
    }

    /**
     * Filter by session type (supports Enum or string)
     */
    public function sessionType(
        SessionType|string|null $type
    ): self {
        if ($type instanceof SessionType) {
            $type = $type->value;
        }

        if ($type) {
            $this->where('session_type', $type);
        }

        return $this;
    }

    /**
     * Filter by specialist
     */
    public function bySpecialist(int $specialistId): self
    {
        return $this->where('conducted_by', $specialistId);
    }

    /**
     * Filter by date range
     */
    public function betweenDates(
        ?string $from,
        ?string $to
    ): self {
        if ($from) {
            $this->whereDate('session_date', '>=', $from);
        }

        if ($to) {
            $this->whereDate('session_date', '<=', $to);
        }

        return $this;
    }

    /**
     * Order by latest session date
     */
    public function latestSession(): self
    {
        return $this->orderByDesc('session_date');
    }

    /**
     * Eager load specialist & user
     */
    public function withRelations(): self
    {
        return $this->with(['specialist.user']);
    }
}
