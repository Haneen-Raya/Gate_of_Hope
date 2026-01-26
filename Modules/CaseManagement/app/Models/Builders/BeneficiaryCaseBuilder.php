<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class BeneficiaryCaseBuilder
 *
 * Custom Query Builder for the BeneficiaryCase model.
 * Provides a fluent API for filtering and querying beneficiary cases.
 * * @package Modules\CaseManagement\Models\Builders
 * @extends Builder<\Modules\CaseManagement\Models\BeneficiaryCase>
 */
class BeneficiaryCaseBuilder extends Builder
{
    /**
     * Filter cases by a specific beneficiary ID.
     *
     * @param int $id The unique identifier of the beneficiary.
     * @return $this
     */
    public function forBeneficiary(int $id): self
    {
        return $this->where('beneficiary_id', $id);
    }

    /**
     * Filter cases by a specific sub-issue ID.
     *
     * @param int $id The unique identifier of the sub-issue.
     * @return $this
     */
    public function withSubIssue(int $id): self
    {
        return $this->where('sub_issue_id', $id);
    }

    /**
     * Filter cases assigned to a specific case manager.
     *
     * @param int $managerId The ID of the assigned manager.
     * @return $this
     */
    public function assignedTo(int $managerId): self
    {
        return $this->where('case_manager_id', $managerId);
    }

    /**
     * Filter cases by region.
     *
     * @param int $regionId The ID of the geographical region.
     * @return $this
     */
    public function inRegion(int $regionId): self
    {
        return $this->where('region_id', $regionId);
    }

    /**
     * Filter cases by their current status (e.g., open, pending, closed).
     *
     * @param string $status The status string.
     * @return $this
     */
    public function withStatus(string $status): self
    {
        return $this->where('status', $status);
    }

    /**
     * Filter cases by priority level (e.g., low, medium, high, urgent).
     *
     * @param string $priority The priority level.
     * @return $this
     */
    public function withPriority(string $priority): self
    {
        return $this->where('priority', $priority);
    }

    /**
     * Filter cases based on their opening date range.
     *
     * @param string|null $from Start date (YYYY-MM-DD).
     * @param string|null $to End date (YYYY-MM-DD).
     * @return $this
     */
    public function openedBetween(?string $from, ?string $to): self
    {
        return $this->when($from, fn($q) => $q->whereDate('opened_at', '>=', $from))
                    ->when($to, fn($q) => $q->whereDate('opened_at', '<=', $to));
    }

    /**
     * Filter cases based on their closing date range.
     *
     * @param string|null $from Start date (YYYY-MM-DD).
     * @param string|null $to End date (YYYY-MM-DD).
     * @return $this
     */
    public function closedBetween(?string $from, ?string $to): self
    {
        return $this->when($from, fn($q) => $q->whereDate('closed_at', '>=', $from))
                    ->when($to, fn($q) => $q->whereDate('closed_at', '<=', $to));
    }

    /**
     * Apply dynamic filters from an HTTP request or associative array.
     * * Supported keys: beneficiary_id, sub_issue_id, case_manager_id,
     * region_id, status, priority, opened_from, opened_to, closed_from, closed_to.
     *
     * @param array<string, mixed> $filters Map of filter keys and values.
     * @return $this
     */
    public function filter(array $filters): self
    {
        return $this
            // 1. Relational Filters
            ->when($filters['beneficiary_id'] ?? null, fn($q, $id) => $q->forBeneficiary((int)$id))
            ->when($filters['sub_issue_id'] ?? null, fn($q, $id) => $q->withSubIssue((int)$id))
            ->when($filters['case_manager_id'] ?? null, fn($q, $id) => $q->assignedTo((int)$id))
            ->when($filters['region_id'] ?? null, fn($q, $id) => $q->inRegion((int)$id))

            // 2. Status & Priority
            ->when($filters['status'] ?? null, fn($q, $s) => $q->withStatus($s))
            ->when($filters['priority'] ?? null, fn($q, $p) => $q->withPriority($p))

            // 3. Temporal Range Filters
            ->openedBetween(
                $filters['opened_from'] ?? null,
                $filters['opened_to'] ?? null
            )
            ->closedBetween(
                $filters['closed_from'] ?? null,
                $filters['closed_to'] ?? null
            );
    }
}
