<?php

namespace Modules\Programs\Services;

use Modules\Programs\Enums\V1\Activity\AttendanceStatus;
use Modules\Programs\Models\ActivityAttendance;
use Modules\Programs\Models\Program;

/**
 * Class ProgramPerformanceReportService
 *
 * Handles generation of program performance analytics reports.
 *
 * This service calculates key metrics such as:
 * - Total activities and sessions
 * - Beneficiary attendance rate
 * - Positive change indicators
 * - Beneficiary insights (most benefited / most in-need groups)
 *
 * @package Modules\Programs\Services
 */
class ProgramPerformanceReportService
{
    /**
     * Generate a full performance report for a given program.
     *
     * @param int $programId
     * @return array<string, mixed>
     */
    public function generate(int $programId): array
    {
        $program = Program::with('activities.activitySessions')->findOrFail($programId);

        $metrics = $this->calculatePerformanceMetrics($programId, $program);

        $positiveIndicators = $this->calculatePositiveIndicators($programId);

        $beneficiaryInsights = $this->generateBeneficiaryInsights($programId);

        return [
            "program" => [
                "id"         => $program->id,
                "name"       => $program->name,
                "start_date" => $program->start_date,
                "end_date"   => $program->end_date,
                "status"     => $program->status,
            ],

            "performance_metrics" => $metrics,

            "positive_change_indicators" => $positiveIndicators,

            "beneficiary_insights" => $beneficiaryInsights,

            "summary" => $this->generateSummary($metrics),

            "generated_at" => now()->toISOString(),
        ];
    }

    /**
     * Calculate the main performance metrics of the program.
     *
     * Includes:
     * - Total activities
     * - Total sessions
     * - Attendance counts
     * - Attendance rate
     *
     * @param int $programId
     * @param Program $program
     * @return array<string, mixed>
     */
    protected function calculatePerformanceMetrics(int $programId, Program $program): array
    {
        $totalActivities = $program->activities->count();

        $totalSessions = $program->activities
            ->flatMap(fn($activity) => $activity->sessions)
            ->count();

        $totalAttendanceRecords = ActivityAttendance::whereHas(
            'activitySession.activity',
            fn($q) => $q->where('program_id', $programId)
        )->count();

        $attendedCount = ActivityAttendance::whereHas(
            'activitySession.activity',
            fn($q) => $q->where('program_id', $programId)
        )->where('attendance_status', AttendanceStatus::ATTENDED)->count();

        $absentCount = $totalAttendanceRecords - $attendedCount;

        $attendanceRate = $totalAttendanceRecords > 0
            ? round($attendedCount / $totalAttendanceRecords, 2)
            : 0;

        $uniqueBeneficiaries = ActivityAttendance::whereHas(
            'activitySession.activity',
            fn($q) => $q->where('program_id', $programId)
        )->distinct('beneficiary_id')->count('beneficiary_id');

        return [
            "total_activities"              => $totalActivities,
            "total_sessions"                => $totalSessions,
            "total_registered_beneficiaries"=> $uniqueBeneficiaries,
            "total_attendance_records"      => $totalAttendanceRecords,
            "attended_count"                => $attendedCount,
            "absent_count"                  => $absentCount,
            "attendance_rate"               => $attendanceRate,
        ];
    }

    /**
     * Calculate positive change indicators for the program.
     *
     * These indicators provide deeper insights beyond raw attendance:
     * - Regular attendance (high engagement)
     * - Dropout rate (missed participation)
     *
     * @param int $programId
     * @return array<string, mixed>
     */
    protected function calculatePositiveIndicators(int $programId): array
    {
        $attendanceGrouped = ActivityAttendance::whereHas(
            'activitySession.activity',
            fn($q) => $q->where('program_id', $programId))
            ->get()
            ->groupBy('beneficiary_id');

        //Regular attendees = beneficiaries who attended 5+ sessions.
        $regularAttendeesCount = $attendanceGrouped
            ->filter(function ($records) {
                return $records->where(
                    'attendance_status',
                    AttendanceStatus::ATTENDED
                )->count() >= 5;
            })
            ->count();

        /**
         * Dropout = beneficiaries who have attendance records
         * but never attended any session.
         */
        $dropoutCount = $attendanceGrouped
            ->filter(function ($records) {
                return $records->where(
                    'attendance_status',
                    AttendanceStatus::ATTENDED
                )->count() === 0;
            })
            ->count();

        $totalBeneficiaries = $attendanceGrouped->count();

        $dropoutRate = $totalBeneficiaries > 0
            ? round(($dropoutCount / $totalBeneficiaries) * 100, 2)
            : 0;

        return [
            "regular_attendees" => [
                "count"      => $regularAttendeesCount,
                "definition" => "Beneficiaries who attended at least 5 sessions.",
            ],

            "dropout_rate" => [
                "value"       => $dropoutRate,
                "description" => "Dropout rate represents beneficiaries who were registered but never attended any session.",
            ],
        ];
    }

    /**
     * Generate beneficiary insights for the program.
     *
     * This section highlights:
     * - Most benefited groups (highest attendance rate)
     * - Most in-need groups (lowest attendance rate)
     *
     * @param int $programId
     * @return array<string, mixed>
     */
    protected function generateBeneficiaryInsights(int $programId): array
    {
        $genderStats = ActivityAttendance::whereHas(
            'activitySession.activity',
            fn($q) => $q->where('program_id', $programId)
            )
            ->join('beneficiaries', 'beneficiaries.id', '=', 'activity_attendances.beneficiary_id')
            ->selectRaw("
                beneficiaries.gender as group_name,
                COUNT(*) as total_records,
                SUM(CASE WHEN attendance_status = 'attended' THEN 1 ELSE 0 END) as attended_count")
            ->groupBy('beneficiaries.gender')
            ->get()
            ->map(function ($row) {
                return [
                    "category"        => "gender",
                    "group"           => $row->group_name,
                    "attendance_rate" => $row->total_records > 0
                        ? round(($row->attended_count / $row->total_records) * 100, 2)
                        : 0,
                ];
            });

        return [
            "most_benefited_groups" => $genderStats->sortByDesc('attendance_rate')->take(1)->values(),

            "most_in_need_groups"   => $genderStats->sortBy('attendance_rate')->take(1)->values(),
        ];
    }

    /**
     * Generate a summary section for decision-makers.
     *
     * @param array<string, mixed> $metrics
     * @return array<string, mixed>
     */
    protected function generateSummary(array $metrics): array
    {
        $effectiveness =
            $metrics["attendance_rate"] >= 75 ? "good" :
            ($metrics["attendance_rate"] >= 50 ? "average" : "weak");

        return [
            "overall_effectiveness" => $effectiveness,
            "key_notes" => [
                "Attendance rate is " . ($metrics["attendance_rate"]) . "%",
                "Total beneficiaries reached: " . $metrics["total_registered_beneficiaries"],
            ],
        ];
    }
}
