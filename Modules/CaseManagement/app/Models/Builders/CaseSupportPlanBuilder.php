<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Custom Query Builder for the CaseSupportPlan Model.
 *
 * This class encapsulates all filtering logic for support plans, 
 * including version control, status toggles, and date range analysis.
 *
 * @extends Builder<\Modules\CaseManagement\Models\CaseSupportPlan>
 */
class CaseSupportPlanBuilder extends Builder
{
    /**
     * Filter by a specific beneficiary case.
     *
     * @param int $caseId
     * @return self
     */
    public function forCase(int $caseId): self
    {
        return $this->where('beneficiary_case_id', $caseId);
    }

    /**
     * Filter only the currently active support plans.
     *
     * @param bool $status
     * @return self
     */
    public function active(bool $status = true): self
    {
        return $this->where('is_active', $status);
    }

    /**
     * Filter by a specific version number of the plan.
     *
     * @param int $version
     * @return self
     */
    public function version(int $version): self
    {
        return $this->where('version', $version);
    }

    /**
     * Filter plans created by a specific user.
     *
     * @param int $userId
     * @return self
     */
    public function createdBy(int $userId): self
    {
        return $this->where('created_by', $userId);
    }

    /**
     * Filter plans that are currently effective (within start and end dates).
     * * @return self
     */
    public function effectiveNow(): self
    {
        $today = now()->toDateString();
        return $this->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    /**
     * Filter plans by their start date range.
     *
     * @param string|null $from
     * @param string|null $to
     * @return self
     */
    public function startedBetween(?string $from, ?string $to): self
    {
        return $this->when($from, fn($q) => $q->whereDate('start_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('start_date', '<=', $to));
    }

    /**
     * Apply dynamic filters from an HTTP request array.
     * * This method orchestrates various scopes to provide a fluid filtering 
     * experience for case support plans, covering relational, temporal, 
     * and versioning constraints.
     *
     * @param array<string, mixed> $filters Associative array containing filter keys.
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            // ---------------------------------------------------
            // 1. Relational Integrity Scopes
            // ---------------------------------------------------
            // Filters data based on foreign key constraints (Case and Author).
            ->when($filters['case_id'] ?? null, fn($q, $id) => $q->forCase((int) $id))
            ->when($filters['created_by'] ?? null, fn($q, $id) => $q->createdBy((int) $id))

            // ---------------------------------------------------
            // 2. Lifecycle & Versioning
            // ---------------------------------------------------
            // Manages the visibility of plans based on their active status 
            // and specific document iterations.
            ->when($filters['version'] ?? null, fn($q, $v) => $q->version((int) $v))
            ->when(isset($filters['is_active']), fn($q) => $q->active((bool) $filters['is_active']))

            // ---------------------------------------------------
            // 3. Temporal Range Filtering
            // ---------------------------------------------------
            // Encapsulates date boundary logic to filter plans by their 
            // initialization period.
            ->startedBetween(
                $filters['start_date_from'] ?? null,
                $filters['start_date_to'] ?? null
            )

            // ---------------------------------------------------
            // 4. Real-time Effectiveness logic
            // ---------------------------------------------------
            // A specialized filter that dynamically calculates if a plan 
            // is currently valid based on the server's current date.
            ->when($filters['only_effective'] ?? null, fn($q) => $q->effectiveNow());
    }
}
