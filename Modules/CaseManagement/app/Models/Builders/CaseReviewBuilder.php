<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\User;

/**
 * Custom Query Builder for the CaseReview Model.
 *
 * This class orchestrates the filtering logic for periodic case evaluations,
 * enabling granular tracking of specialist performance and beneficiary progress.
 *
 * @extends Builder<\Modules\CaseManagement\Models\CaseReview>
 */
class CaseReviewBuilder extends Builder
{
    /**
     * Filter reviews for a specific beneficiary case.
     *
     * @param int $caseId
     * @return self
     */
    public function forCase(int $caseId): self
    {
        return $this->where('beneficiary_case_id', $caseId);
    }

    /**
     * Filter reviews conducted by a specific specialist.
     *
     * @param int $specialistId
     * @return self
     */
    public function bySpecialist(int $specialistId): self
    {
        return $this->where('specialist_id', $specialistId);
    }

    /**
     * Filter reviews by the recorded progress status.
     *
     * @param string $status (e.g., 'improving', 'stable', 'deteriorating')
     * @return self
     */
    public function progressStatus(string $status): self
    {
        return $this->where('progress_status', $status);
    }

    /**
     * Filter reviews based on the review date range.
     *
     * @param string|null $start
     * @param string|null $end
     * @return self
     */
    public function reviewedBetween(?string $start, ?string $end): self
    {
        return $this->when($start, fn($q) => $q->whereDate('reviewed_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('reviewed_at', '<=', $end));
    }

    /**
     * Scope to retrieve only the most recent review for each case.
     * Useful for dashboard snapshots.
     *
     * @return self
     */
    public function latestReviews(): self
    {
        return $this->latest('reviewed_at');
    }

    /**
     * Orchestrate dynamic query filtering for Case Reviews.
     * * Handles key analytical dimensions:
     * 1. **Case Ownership:** Linking reviews to specific beneficiary cases.
     * 2. **Specialist Attribution:** Tracking reviews by professional identity.
     * 3. **Clinical/Social Progress:** Filtering by progress trajectories.
     * 4. **Temporal Auditing:** Analyzing review dates for compliance and reporting.
     *
     * @param array<string, mixed> $filters {
     * @var int|null    $case_id         Filter by beneficiary case identifier.
     * @var int|null    $specialist_id   Filter by the conducting specialist.
     * @var string|null $progress_status The outcome of the review (e.g., 'improving').
     * @var string|null $from_date       Start of the review period (YYYY-MM-DD).
     * @var string|null $to_date         End of the review period (YYYY-MM-DD).
     * @var bool|null   $latest_only     If true, orders by most recent.
     * }
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            // ---------------------------------------------------
            // 1. Relational Scoping
            // ---------------------------------------------------
            ->when($filters['case_id'] ?? null, fn($q, $id) => $q->forCase((int) $id))
            ->when($filters['specialist_id'] ?? null, fn($q, $id) => $q->bySpecialist((int) $id))

            // ---------------------------------------------------
            // 2. Performance & Progress Analysis
            // ---------------------------------------------------
            ->when($filters['progress_status'] ?? null, fn($q, $status) => $q->progressStatus($status))

            // ---------------------------------------------------
            // 3. Chronological Filtering
            // ---------------------------------------------------
            ->reviewedBetween(
                $filters['from_date'] ?? null,
                $filters['to_date'] ?? null
            )

            // ---------------------------------------------------
            // 4. Sorting & Ordering
            // ---------------------------------------------------
            ->when($filters['latest_only'] ?? true, fn($q) => $q->latestReviews());
    }
}