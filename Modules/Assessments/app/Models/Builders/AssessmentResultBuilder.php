<?php

namespace Modules\Assessments\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Custom Query Builder for the AssessmentResult Model.
 *
 * This class orchestrates the complex filtering logic for beneficiary assessments,
 * enabling precise tracking of vulnerability scores, priority levels, and historical trends.
 *
 * @extends Builder<\Modules\Assessments\Models\AssessmentResult>
 */
class AssessmentResultBuilder extends Builder
{
    /**
     * Filter results for a specific beneficiary.
     *
     * @param int $beneficiaryId
     * @return self
     */
    public function forBeneficiary(int $beneficiaryId): self
    {
        return $this->where('beneficiary_id', $beneficiaryId);
    }

    /**
     * Filter results by a specific issue or category type.
     *
     * @param int $issueTypeId
     * @return self
     */
    public function forIssueType(int $issueTypeId): self
    {
        return $this->where('issue_type_id', $issueTypeId);
    }

    /**
     * Scope to retrieve only the most recent assessment for each category.
     * Crucial for active case management and dashboard reporting.
     *
     * @return self
     */
    public function onlyLatest(): self
    {
        return $this->where('is_latest', true);
    }

    /**
     * Filter by the final priority level assigned after specialist review.
     *
     * @param string $priority (e.g., 'high', 'medium', 'low')
     * @return self
     */
    public function priority(string $priority): self
    {
        return $this->where('priority_final', $priority);
    }

    /**
     * Filter results within a specific range of normalized scores.
     * Useful for identifying beneficiaries above a certain vulnerability threshold.
     *
     * @param float|null $min
     * @param float|null $max
     * @return self
     */
    public function scoreBetween(?float $min, ?float $max): self
    {
        return $this->when($min, fn($q) => $q->where('normalized_score', '>=', $min))
            ->when($max, fn($q) => $q->where('normalized_score', '<=', $max));
    }

    /**
     * Filter assessments based on the date they were conducted.
     *
     * @param string|null $start
     * @param string|null $end
     * @return self
     */
    public function assessedBetween(?string $start, ?string $end): self
    {
        return $this->when($start, fn($q) => $q->whereDate('assessed_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('assessed_at', '<=', $end));
    }

    /**
     * Orchestrate dynamic query filtering for Assessment Results.
     * * Handles four analytical dimensions:
     * 1. **Structural Scoping:** Linking results to beneficiaries and issue types.
     * 2. **Performance Metrics:** Filtering by normalized scores and priority levels.
     * 3. **Version Control:** Isolating the latest records vs historical data.
     * 4. **Temporal Auditing:** Range filtering by assessment date.
     *
     * @param array<string, mixed> $filters {
     * @var int|null    $beneficiary_id  Filter by beneficiary identifier.
     * @var int|null    $issue_type_id   Filter by specific problem/category.
     * @var string|null $priority         The finalized priority level.
     * @var float|null  $min_score        Minimum normalized score threshold.
     * @var float|null  $max_score        Maximum normalized score threshold.
     * @var string|null $from_date        Assessment start date (YYYY-MM-DD).
     * @var string|null $to_date          Assessment end date (YYYY-MM-DD).
     * @var bool|null   $latest_only      If true, filters by is_latest = 1.
     * }
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            // ---------------------------------------------------
            // 1. Relational & Ownership Scoping
            // ---------------------------------------------------
            ->when($filters['beneficiary_id'] ?? null, fn($q, $id) => $q->forBeneficiary((int) $id))
            ->when($filters['issue_type_id'] ?? null, fn($q, $id) => $q->forIssueType((int) $id))

            // ---------------------------------------------------
            // 2. Metrics & Vulnerability Analysis
            // ---------------------------------------------------
            ->when($filters['priority'] ?? null, fn($q, $p) => $q->priority($p))
            ->scoreBetween(
                $filters['min_score'] ?? null,
                $filters['max_score'] ?? null
            )

            // ---------------------------------------------------
            // 3. Versioning & Timeline
            // ---------------------------------------------------
            ->when($filters['latest_only'] ?? true, fn($q) => $q->onlyLatest())
            ->assessedBetween(
                $filters['from_date'] ?? null,
                $filters['to_date'] ?? null
            )

            // ---------------------------------------------------
            // 4. Default Ordering
            // ---------------------------------------------------
            ->latest('assessed_at');
    }
}