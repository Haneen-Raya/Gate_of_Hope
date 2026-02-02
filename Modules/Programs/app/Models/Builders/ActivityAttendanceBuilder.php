<?php

namespace Modules\Programs\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ActivityAttendanceBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters on the ActivityAttendance model.
 *
 * Provides a fluent interface to filter activity attendance
 * records using the fields explicitly defined in this builder:
 *
 * - activity_session_id : Filter by activity session ID
 * - beneficiary_id      : Filter by beneficiary ID
 * - recorded_by         : Filter by the user who recorded the attendance
 * - attendance_status   : Filter by the attendance_status column
 *
  *
 * @package Modules\Programs\Models\Builders
 *
 * @method self filterActivitySession(?int $activitySessionId)
 * @method self filterBeneficiary(?int $beneficiaryId)
 * @method self filterRecorder(?int $recorderId)
 * @method self filterAttendancestatus(?string $status)
 * @method self filter(array $filters)
 */
class ActivityAttendanceBuilder extends Builder
{
    /**
     * Filter attendances by activity session ID.
     *
     * Applies filtering using the foreign key activity_session_id.
     *
     * @param int|null $activitySessionId
     *
     * @return self
     */
    public function filterActivitySession(?int $activitySessionId): self
    {
        return $this->when($activitySessionId, fn($q) => $q->where('activity_session_id', $activitySessionId));
    }

    /**
     * Filter attendances by beneficiary ID.
     *
     * Applies filtering using the foreign key beneficiary_id.
     *
     * @param int|null $beneficiaryId
     *
     * @return self
     */
    public function filterBeneficiary(?int $beneficiaryId): self
    {
        return $this->when($beneficiaryId, fn($q) => $q->where('beneficiary_id', $beneficiaryId));
    }

    /**
     * Filter attendances by recorded_by ID.
     *
     * Applies filtering using the foreign key recorded_by.
     *
     * @param int|null $recorderId
     *
     * @return self
     */
    public function filterRecorder(?int $recorderId): self
    {
        return $this->when($recorderId, fn($q) => $q->where('recorded_by', $recorderId));
    }

    /**
     * Filter attendances by attendance status.
     *
     * This filter matches the attendance_status column exactly.
     *
     * @param string|null $status
     *
     * @return self
     */
    public function filterAttendancestatus(?string $status): self
    {
        return $this->when($status, fn($q) => $q->where('attendance_status', $status));
    }


    /**
     * Apply dynamic filters on attendances.
     *
     * This is the main entry point for applying multiple filters
     * based on request parameters.
     *
     * Supported filters:
     * - activity_session_id     : int|null
     * - beneficiary_id          : int|null
     * - recorded_by             : int|null
     * - attendance_status       : string|null
     *
     * @param array<string, mixed> $filters
     *
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            ->filterActivitySession($filters['activity_session_id'] ?? null)
            ->filterBeneficiary($filters['beneficiary_id'] ?? null)
            ->filterRecorder($filters['recorded_by'] ?? null)
            ->filterAttendancestatus($filters['attendance_status'] ?? null);
    }
}
