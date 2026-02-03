<?php

namespace Modules\Entities\Reports\Donor;

use Carbon\Carbon;
use Modules\Assessments\Models\AssessmentResult;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\Entities\Models\ProgramFunding;
use Modules\Programs\Models\ActivityAttendance;
use Modules\Programs\Models\Program;

/**
 * Class DonorReportAggregator
 *
 * Aggregates data for generating a donor report.
 * Responsible for building the complete snapshot of a donor report,
 * including meta information, funding usage, beneficiaries, attendance,
 * quantitative and qualitative outcomes.
 *
 * @package Modules\Entities\Reports\Donor
 */
class DonorReportAggregator
{
    /**
     * Build a full donor report array.
     *
     * @param int $donorId ID of the donor entity
     * @param int $programId ID of the program
     * @param Carbon $from Start date of the reporting period
     * @param Carbon $to End date of the reporting period
     *
     * @return array Complete report structure with keys:
     *  - meta
     *  - funding
     *  - beneficiaries
     *  - attendance
     *  - outcomes_quantitative
     *  - outcomes_qualitative
     */
    public function build(int $donorId, int $programId, Carbon $from, Carbon $to): array
    {
        return [
            'meta' => $this->meta($donorId, $programId, $from, $to),
            'funding' => $this->fundingUsage($donorId, $programId, $from, $to),
            'beneficiaries' => $this->beneficiariesStats($programId, $from, $to),
            'attendance' => $this->attendanceStats($programId, $from, $to),
            'outcomes_quantitative' => $this->quantitativeOutcomes($programId, $from, $to),
            'outcomes_qualitative' => $this->qualitativeOutcomes($programId, $from, $to),
        ];
    }

    /* ------------------------------------------------------------------ */
    /* META INFORMATION */
    /* ------------------------------------------------------------------ */

    /**
     * Generate meta information for the report.
     *
     * @param int $donorId
     * @param int $programId
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return array ['donor' => string, 'program' => string, 'period' => ['from'=>string, 'to'=>string]]
     */
    protected function meta(int $donorId, int $programId, Carbon $from, Carbon $to): array
    {
        $funding = ProgramFunding::where('donor_entity_id', $donorId)
            ->where('program_id', $programId)
            ->first();

        return [
            'donor' => optional($funding?->donor)->name ?? 'Unknown',
            'program' => Program::find($programId)?->name ?? 'Unknown',
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
        ];
    }

    /* ------------------------------------------------------------------ */
    /* FUNDING USAGE */
    /* ------------------------------------------------------------------ */

    /**
     * Calculate funding usage for the reporting period.
     *
     * @param int $donorId
     * @param int $programId
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return array ['allocated', 'used', 'remaining', 'currency', 'usage_percentage']
     */
    protected function fundingUsage(int $donorId, int $programId, Carbon $from, Carbon $to): array
    {
        $funding = ProgramFunding::where('donor_entity_id', $donorId)
            ->where('program_id', $programId)
            ->whereDate('start_date', '<=', $to)
            ->whereDate('end_date', '>=', $from)
            ->first();

        if (!$funding) {
            return [
                'allocated' => 0,
                'used' => 0,
                'remaining' => 0,
                'currency' => null,
                'usage_percentage' => 0,
            ];
        }

        $used = $this->calculateUsedAmount($programId, $from, $to);

        return [
            'allocated' => (float) $funding->amount,
            'used' => $used,
            'remaining' => max(0, (float) $funding->amount - $used),
            'currency' => $funding->currency,
            'usage_percentage' => $funding->amount > 0
                ? round(($used / $funding->amount) * 100, 2)
                : 0,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* BENEFICIARIES */
    /* ------------------------------------------------------------------ */

    /**
     * Calculate total and active beneficiaries in the program during the period.
     *
     * @param int $programId
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return array ['total'=>int, 'active'=>int]
     */
    protected function beneficiariesStats(int $programId, Carbon $from, Carbon $to): array
    {
        $baseQuery = Beneficiary::whereHas(
            'activityAttendances.activitySession.activity.program',
            fn ($q) => $q->where('programs.id', $programId)
        );

        $total = (clone $baseQuery)->distinct('beneficiaries.id')->count('beneficiaries.id');

        $active = (clone $baseQuery)
            ->whereHas('activityAttendances', function ($q) use ($from, $to) {
                $q->whereBetween('activity_attendances.created_at', [$from, $to])
                  ->where('attendance_status', 'present');
            })
            ->distinct('beneficiaries.id')
            ->count('beneficiaries.id');

        return [
            'total' => $total,
            'active' => $active,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* ATTENDANCE */
    /* ------------------------------------------------------------------ */

    /**
     * Generate attendance statistics for the period.
     *
     * @param int $programId
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return array ['total_records'=>int, 'attendance_rate'=>float, 'attendance_breakdown'=>array]
     */
    protected function attendanceStats(int $programId, Carbon $from, Carbon $to): array
    {
        $attendances = ActivityAttendance::whereHas(
            'activitySession.activity.program',
            fn ($q) => $q->where('programs.id', $programId)
        )
        ->whereBetween('activity_attendances.created_at', [$from, $to])
        ->get();

        $total = $attendances->count();

        return [
            'total_records' => $total,
            'attendance_rate' => $total > 0
                ? round(($attendances->where('attendance_status', 'present')->count() / $total) * 100, 2)
                : 0,
            'attendance_breakdown' => [
                'present' => $attendances->where('attendance_status', 'present')->count(),
                'absent' => $attendances->where('attendance_status', 'absent')->count(),
                'excused' => $attendances->where('attendance_status', 'excused')->count(),
            ],
        ];
    }

    /* ------------------------------------------------------------------ */
    /* OUTCOMES – QUANTITATIVE */
    /* ------------------------------------------------------------------ */

    /**
     * Calculate quantitative outcomes for the program.
     *
     * @param int $programId
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return array ['avg_improvement'=>float, 'positive_change_rate'=>float]
     */
    protected function quantitativeOutcomes(int $programId, Carbon $from, Carbon $to): array
    {
        $assessments = AssessmentResult::whereHas(
            'beneficiary.activityAttendances.activitySession.activity.program',
            fn ($q) => $q->where('programs.id', $programId)
        )
        ->whereBetween('created_at', [$from, $to])
        ->get();

        return [
            'avg_improvement' => round($assessments->avg('score_change') ?? 0, 2),
            'positive_change_rate' => $assessments->count() > 0
                ? round(($assessments->where('score_change', '>', 0)->count() / $assessments->count()) * 100, 2)
                : 0,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* OUTCOMES – QUALITATIVE */
    /* ------------------------------------------------------------------ */

    /**
     * Collect qualitative notes from activity attendances.
     *
     * @param int $programId
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return array List of qualitative notes
     */
    protected function qualitativeOutcomes(int $programId, Carbon $from, Carbon $to): array
    {
        return ActivityAttendance::whereHas(
            'activitySession.activity.program',
            fn ($q) => $q->where('programs.id', $programId)
        )
        ->whereNotNull('notes')
        ->whereBetween('created_at', [$from, $to])
        ->pluck('notes')
        ->toArray();
    }

    /* ------------------------------------------------------------------ */
    /* COST CALCULATION */
    /* ------------------------------------------------------------------ */

    /**
     * Estimate used amount based on activity attendances.
     *
     * @param int $programId
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return float Total used funding estimate
     */
    protected function calculateUsedAmount(int $programId, Carbon $from, Carbon $to): float
    {
        $count = ActivityAttendance::whereHas(
            'activitySession.activity.program',
            fn ($q) => $q->where('programs.id', $programId)
        )
        ->whereBetween('created_at', [$from, $to])
        ->count();

        $costPerSession = 50;

        return round($count * $costPerSession, 2);
    }
}
