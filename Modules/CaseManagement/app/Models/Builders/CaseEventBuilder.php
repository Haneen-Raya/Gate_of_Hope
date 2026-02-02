<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Custom Query Builder for CaseEvent Model.
 *
 * Encapsulates all timeline filtering, searching, and scoping logic
 * for beneficiary case events.
 *
 * @extends Builder<\Modules\CaseManagement\Models\CaseEvent>
 */
class CaseEventBuilder extends Builder
{
    /* -----------------------------------------------------------------
     | Core Scopes
     |-----------------------------------------------------------------*/

    /**
     * Restrict events to a specific beneficiary case.
     */
    public function forCase(int $caseId): self
    {
        return $this->where('beneficiary_case_id', $caseId);
    }

    /**
     * Restrict events to a specific beneficiary.
     */
    public function forBeneficiary(int $beneficiaryId): self
    {
        return $this->where('beneficiary_id', $beneficiaryId);
    }

    /**
     * Filter events by one or more event types.
     *
     * @param array|string $types
     */
    public function ofType(array|string $types): self
    {
        return $this->whereIn(
            'event_tag',
            (array) $types
        );
    }

    /**
     * Exclude specific event types.
     */
    public function exceptType(array|string $types): self
    {
        return $this->whereNotIn(
            'event_tag',
            (array) $types
        );
    }

    /**
     * Filter events caused by a specific user.
     */
    public function causedBy(int $userId): self
    {
        return $this->where('actor_id', $userId);
    }

    /* -----------------------------------------------------------------
     | Time-based Scopes
     |-----------------------------------------------------------------*/

    /**
     * Filter events that occurred within a date range.
     */
    public function occurredBetween(?string $from, ?string $to): self
    {
        return $this
            ->when(
                $from,
                fn($q) =>
                $q->whereDate('occurred_at', '>=', $from)
            )
            ->when(
                $to,
                fn($q) =>
                $q->whereDate('occurred_at', '<=', $to)
            );
    }

    /**
     * Filter events that occurred after a given date.
     */
    public function occurredAfter(string $date): self
    {
        return $this->whereDate('occurred_at', '>=', $date);
    }

    /**
     * Filter events that occurred before a given date.
     */
    public function occurredBefore(string $date): self
    {
        return $this->whereDate('occurred_at', '<=', $date);
    }

    /* -----------------------------------------------------------------
     | Ordering Scopes
     |-----------------------------------------------------------------*/

    /**
     * Order timeline chronologically (newest first).
     */
    public function latestFirst(): self
    {
        return $this->orderByDesc('occurred_at');
    }

    /**
     * Order timeline chronologically (oldest first).
     */
    public function oldestFirst(): self
    {
        return $this->orderBy('occurred_at');
    }

    /* -----------------------------------------------------------------
     | High-level Filter Aggregator
     |-----------------------------------------------------------------*/

    /**
     * Apply dynamic filters for case timeline.
     *
     * @param array<string, mixed> $filters
     */
    public function filter(array $filters): self
    {
        return $this
            // --------------------------------------------
            // Case / Beneficiary Scoping
            // --------------------------------------------
            ->when(
                $filters['beneficiary_case_id'] ?? null,
                fn($q, $id) => $q->forCase((int) $id)
            )
            ->when(
                $filters['beneficiary_id'] ?? null,
                fn($q, $id) => $q->forBeneficiary((int) $id)
            )

            // --------------------------------------------
            // Event Classification
            // --------------------------------------------
            ->when(
                $filters['types'] ?? null,
                fn($q, $types) => $q->ofType($types)
            )
            ->when(
                $filters['exclude_types'] ?? null,
                fn($q, $types) => $q->exceptType($types)
            )

            // --------------------------------------------
            // Actor Filtering
            // --------------------------------------------
            ->when(
                $filters['causer_id'] ?? null,
                fn($q, $id) => $q->causedBy((int) $id)
            )

            // --------------------------------------------
            // Date Period
            // --------------------------------------------
            ->occurredBetween(
                $filters['from'] ?? null,
                $filters['to'] ?? null
            )

            // --------------------------------------------
            // Ordering
            // --------------------------------------------
            ->when(
                ($filters['order'] ?? 'desc') === 'asc',
                fn($q) => $q->oldestFirst(),
                fn($q) => $q->latestFirst()
            );
    }
}
