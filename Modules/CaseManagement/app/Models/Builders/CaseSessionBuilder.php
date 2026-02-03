<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\CaseManagement\Enums\V1\SessionType;

/**
 * Class CaseSessionBuilder
 *
 * Provides a fluent interface for querying CaseSession records.
 * This builder encapsulates session-specific filtering logic, including specialist
 * performance tracking and temporal session analysis.
 *
 * @extends Builder<\Modules\CaseManagement\Models\CaseSession>
 */
class CaseSessionBuilder extends Builder
{
    /**
     * Filter sessions by a specific beneficiary case.
     * * @param int $caseId
     * @return self
     */
    public function forCase(int $caseId): self
    {
        return $this->where('beneficiary_case_id', $caseId);
    }

    /**
     * Filter by session type, supporting both Enum instances and raw strings.
     * * @param SessionType|string|null $type
     * @return self
     */
    public function sessionType(SessionType|string|null $type): self
    {
        if ($type instanceof SessionType) {
            $type = $type->value;
        }

        if ($type) {
            $this->where('session_type', $type);
        }

        return $this;
    }

    /**
     * Filter sessions conducted by a specific specialist.
     * Useful for workload and performance reporting.
     * * @param int $specialistId
     * @return self
     */
    public function bySpecialist(int $specialistId): self
    {
        return $this->where('conducted_by', $specialistId);
    }

    /**
     * Filter sessions within a specific date range.
     * * @param string|null $from Start date (Y-m-d)
     * @param string|null $to End date (Y-m-d)
     * @return self
     */
    public function betweenDates(?string $from, ?string $to): self
    {
        if ($from) {
            $this->whereDate('session_date', '>=', $from);
        }

        if ($to) {
            $this->whereDate('session_date', '<=', $to);
        }

        return $this;
    }

    /**
     * Scope to order results by the most recent session date.
     * * @return self
     */
    public function latestSession(): self
    {
        return $this->orderByDesc('session_date');
    }

    /**
     * Eager load critical relations to prevent N+1 query issues.
     * Includes the specialist and their associated user profile.
     * * @return self
     */
    public function withRelations(): self
    {
        return $this->with(['specialist.user']);
    }
}
