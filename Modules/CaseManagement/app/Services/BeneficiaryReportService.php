<?php

namespace Modules\CaseManagement\Services;

use Modules\CaseManagement\Models\BeneficiaryCase;
use Modules\CaseManagement\Enums\V1\PlanStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

/**
 * Class BeneficiaryReportService
 * * @package Modules\CaseManagement\Services
 * @author Your Name/Team
 * @version 1.1.0
 * * DESCRIPTION:
 * This high-performance service engine is responsible for orchestrating and
 * aggregating complex multi-relational data into a consolidated 'Professional Report'.
 * It encapsulates the business logic for progress metrics, milestone tracking,
 * and chronological event auditing for individual beneficiary cases.
 */
class BeneficiaryReportService
{
    /**
     * Generate an ultra-comprehensive professional report for a specific case.
     * * This method utilizes Eager Loading with closure constraints to optimize
     * database queries, preventing N+1 issues while fetching deep relationships
     * across 6+ different tables.
     * * @param int $id The unique identifier of the BeneficiaryCase.
     * @return array{
     * metadata: array,
     * metrics: array,
     * milestones: array,
     * timeline: array
     * } A structured associative array containing holistic case intelligence.
     * * @throws ModelNotFoundException If the Case ID does not exist in the persistence layer.
     */
    public function generateProfessionalReport(int $id): array
    {
        $case = BeneficiaryCase::with([
            'beneficiary.user',
            'caseManager:id,name',
            'issueType',
            'caseSupportPlans.casePlansGoals',
            'caseReviews.specialist.user',
            'caseEvents' => fn($q) => $q->latest('occurred_at')->limit(10)
        ])->findOrFail($id);

        return [
            'metadata'   => $this->formatHeader($case),
            'metrics'    => $this->calculateStats($case),
            'milestones' => $this->formatMilestones($case),
            'timeline'   => $this->formatTimeline($case),
        ];
    }

    /**
     * Compile administrative metadata and case identification headers.
     * * @param BeneficiaryCase $case The hydrated Eloquent model instance.
     * @return array{
     * case_reference: string,
     * beneficiary_name: string,
     * system_code: string,
     * issue_type: mixed,
     * assigned_manager: string,
     * report_date: string
     * }
     */
    private function formatHeader(BeneficiaryCase $case): array
    {
        return [
            'case_reference'   => "REF-{$case->id}",
            'beneficiary_name' => $case->beneficiary->user->name ?? 'N/A',
            'system_code'      => $case->beneficiary->system_code ?? 'N/A',
            'issue_type'       => $case->issueType->name ?? 'N/A',
            'assigned_manager' => $case->caseManager->name ?? 'N/A',
            'report_date'      => now()->format('Y-m-d'),
        ];
    }

    /**
     * Compute analytical KPIs and quantitative progress metrics.
     * * Calculations involve cross-referencing plan goals with status Enums
     * and deriving the current trajectory from the latest clinical review.
     * * @param BeneficiaryCase $case
     * @return array{
     * completion_percentage: string,
     * current_trajectory: string,
     * current_status: string,
     * activities_count: int
     * }
     */
    private function calculateStats(BeneficiaryCase $case): array
    {
        $allGoals = $case->caseSupportPlans->flatMap->casePlansGoals;
        $total    = $allGoals->count();
        $achieved = $allGoals->where('status', PlanStatus::ACHIEVED)->count();

        $latestReview = $case->caseReviews()->latest('reviewed_at')->first();

        return [
            'completion_percentage' => $total > 0 ? round(($achieved / $total) * 100) . '%' : '0%',
            'current_trajectory'    => $latestReview ? $latestReview->progress_status->value : 'N/A',
            'current_status'        => $case->status->value,
            'activities_count'      => $case->caseEvents->count(),
        ];
    }

    /**
     * Transform raw event logs into a chronological activity audit trail.
     * * @param BeneficiaryCase $case
     * @return array<int, array{
     * activity: string,
     * date: string,
     * details: array
     * }>
     */
    private function formatTimeline(BeneficiaryCase $case): array
    {
        return $case->caseEvents->map(fn($event) => [
            'activity' => $event->event_tag->value,
            'date'     => $event->occurred_at->format('Y-m-d H:i'),
            'details'  => $event->payload
        ])->values()->all();
    }

    /**
     * Map out qualitative milestones including plan goals and specialist reviews.
     * * Processes target dates and review timestamps using Carbon for ISO-8601
     * standard compliance in the final output.
     * * @param BeneficiaryCase $case
     * @return array{
     * goals: array,
     * reviews: array
     * }
     */
    private function formatMilestones(BeneficiaryCase $case): array
    {
        return [
            'goals' => $case->caseSupportPlans->flatMap->casePlansGoals->map(fn($goal) => [
                'goal'   => $goal->goal_description,
                'status' => $goal->status->value,
                'target' => $goal->target_date ? Carbon::parse($goal->target_date)->format('Y-m-d') : null,
            ])->values()->all(),

            'reviews' => $case->caseReviews->take(5)->map(fn($rev) => [
                'date'    => $rev->reviewed_at ? Carbon::parse($rev->reviewed_at)->format('Y-m-d') : 'N/A',
                'status'  => $rev->progress_status->value,
                'comment' => $rev->notes,
                'by'      => $rev->specialist->user->name ?? 'System'
            ])->values()->all(),
        ];
    }
}
