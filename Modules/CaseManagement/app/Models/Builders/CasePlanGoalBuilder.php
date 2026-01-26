<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\User;

/**
 * Custom Query Builder for the CasePlanGoal Model.
 *
 * This class orchestrates the filtering logic for specific goals within support plans,
 * providing advanced scopes for tracking progress, deadlines, and achievement metrics.
 *
 * @extends Builder<\Modules\CaseManagement\Models\CasePlanGoal>
 */
class CasePlanGoalBuilder extends Builder
{
    /**
     * Filter goals belonging to a specific support plan.
     *
     * @param int $planId
     * @return self
     */
    public function forPlan(int $planId): self
    {
        return $this->where('plan_id', $planId);
    }

    /**
     * Filter goals by their current operational status.
     *
     * @param string $status (e.g., 'pending', 'in-progress', 'achieved', 'cancelled')
     * @return self
     */
    public function status(string $status): self
    {
        return $this->where('status', $status);
    }

    /**
     * Scope to retrieve only goals that have been successfully achieved.
     *
     * @return self
     */
    public function achieved(): self
    {
        return $this->whereNotNull('achieved_at')
            ->where('status', 'achieved');
    }

    /**
     * Filter goals that have passed their target date without being achieved.
     *
     * @return self
     */
    public function overdue(): self
    {
        return $this->whereNull('achieved_at')
            ->whereDate('target_date', '<', now()->toDateString())
            ->where('status', '!=', 'cancelled');
    }

    /**
     * Filter goals based on their target achievement date range.
     *
     * @param string|null $from
     * @param string|null $to
     * @return self
     */
    public function targetBetween(?string $from, ?string $to): self
    {
        return $this->when($from, fn($q) => $q->whereDate('target_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('target_date', '<=', $to));
    }

    // public function accessibleBy(User $user): self
    // {
    //     if ($user->hasRole('admin')) {
    //         return $this;
    //     }

    //     return $this->whereHas('caseSupportPlan', function ($q) use ($user) {
    //         $q->where('created_by', $user->id);
    //     });
    // }

    /**
     * Orchestrate dynamic query filtering for Case Plan Goals.
     * 
     * This method acts as the central engine for refining goal-related queries.
     * 
     * It intelligently handles four key dimensions:
     * 1. **Relational Context:** Scoping goals to their parent Support Plan.
     * 2. **Lifecycle State:** Filtering by operational status (Pending, Achieved, or Overdue).
     * 3. **Chronological Deadlines:** Analyzing target dates to identify upcoming or missed milestones.
     * 4. **Success Metrics:** Verifying achievement timestamps for performance auditing.
     *
     * @param array<string, mixed> $filters {
     * @var int|null    $plan_id       Filter by a specific parent support plan.
     * @var string|null $status        The current state (e.g., 'pending', 'cancelled').
     * @var mixed       $only_achieved Presence triggers filtering for completed goals.
     * @var mixed       $only_overdue  Presence triggers filtering for goals past their target_date.
     * @var string|null $target_from   Beginning of the target date range (YYYY-MM-DD).
     * @var string|null $target_to     End of the target date range (YYYY-MM-DD).
     * @var string|null $achieved_at   The exact date of successful completion.
     * }
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            // ---------------------------------------------------
            // 1. Structural Ownership
            // ---------------------------------------------------
            // Ensures strict referential integrity by scoping goals to a specific plan.
            ->when($filters['plan_id'] ?? null, fn($q, $id) => $q->forPlan((int) $id))

            // ---------------------------------------------------
            // 2. Progress & Lifecycle Tracking
            // ---------------------------------------------------
            // Dynamically resolves the goal's status or identifies performance anomalies 
            // such as overdue milestones.
            ->when($filters['status'] ?? null, fn($q, $s) => $q->status($s))
            ->when($filters['only_achieved'] ?? null, fn($q) => $q->achieved())
            ->when($filters['only_overdue'] ?? null, fn($q) => $q->overdue())

            // ---------------------------------------------------
            // 3. Deadline Analysis
            // ---------------------------------------------------
            // Provides chronological filtering to support operational reporting 
            // and upcoming deadline alerts.
            ->targetBetween(
                $filters['target_from'] ?? null,
                $filters['target_to'] ?? null
            )

            // ---------------------------------------------------
            // 4. Achievement Synchronization
            // ---------------------------------------------------
            // Validates when a goal transitioned to a completed state, 
            // essential for timeline-based auditing.
            ->when($filters['achieved_at'] ?? null, fn($q, $date) => $q->whereDate('achieved_at', $date));
    }
}
