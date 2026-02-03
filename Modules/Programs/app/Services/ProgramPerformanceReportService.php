<?php

namespace Modules\Programs\Services;

use Modules\Programs\Enums\Api\V1\Activity\AttendanceStatus;
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
     * - Regular attendance
     * - Dropout estimation
     *
     * @param int $programId
     * @return array<string, mixed>
     */
    protected function calculatePositiveIndicators(int $programId): array
    {
        $regularAttendees =  ActivityAttendance::with('activitySession.activity')
                ->where('attendance_status', 'attended')
                ->whereHas('activitySession.activity', function ($query) {
                    $query->where('program_id', 1);
                })
                ->get()
                ->groupBy('beneficiary_id')
                ->filter(function ($attendances) {
                    return $attendances->count() >= 5; 
                });

        return [
            "regular_attendees" => [
                "count"      => $regularAttendees,
                "definition" => "Beneficiaries who attended at least 5 sessions",
            ],

            "dropout_rate" => [
                "value"       => 0.10,
                "description" => "Placeholder until dropout logic is implemented",
            ],
        ];
    }

    /**
     * Generate beneficiary insights for the program.
     *
     * This section highlights:
     * - Most benefited groups
     * - Most in-need groups
     *
     * Currently simplified as placeholders.
     *
     * @param int $programId
     * @return array<string, mixed>
     */
    protected function generateBeneficiaryInsights(int $programId): array
    {
        return [
            "most_benefited_groups" => [
                [
                    "category"        => "gender",
                    "group"           => "Women",
                    "attendance_rate" => 0.85,
                ],
            ],

            "most_in_need_groups" => [
                [
                    "category"        => "age_range",
                    "group"           => "18-25",
                    "attendance_rate" => 0.45,
                    "note"            => "Low attendance indicates need for follow-up",
                ],
            ],
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
            $metrics["attendance_rate"] >= 0.75 ? "good" :
            ($metrics["attendance_rate"] >= 0.50 ? "average" : "weak");

        return [
            "overall_effectiveness" => $effectiveness,
            "key_notes" => [
                "Attendance rate is " . ($metrics["attendance_rate"] * 100) . "%",
                "Total beneficiaries reached: " . $metrics["total_registered_beneficiaries"],
            ],
        ];
    }
}
