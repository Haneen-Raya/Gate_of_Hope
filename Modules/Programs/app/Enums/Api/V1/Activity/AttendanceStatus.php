<?php

namespace Modules\Programs\Enums\Api\V1\Activity;

/**
 * @Enum AttendanceStatus
 *
 * Represents the participation status of a beneficiary
 * in an activity session attendance record.
 *
 * Attendance statuses are used to track whether the beneficiary:
 * - Attended the session
 * - Was absent
 * - Was excused
 * - Cancelled participation
 *
 * This supports monitoring, reporting, and program evaluation.
 *
 * @method static array all() Returns all enum values as strings.
 */
enum AttendanceStatus: string
{
    /** Beneficiary attended the session successfully. */
    case ATTENDED = 'attended';

    /** Beneficiary did not attend without excuse. */
    case ABSENT = 'absent';

    /** Beneficiary absence was excused with valid reason. */
    case EXCUSED = 'excused';


    /**
     * Get a human-readable label for each attendance status.
     *
     * Intended for UI display, reports, and API responses.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ATTENDED   => 'Attended',
            self::ABSENT     => 'Absent',
            self::EXCUSED    => 'Excused Absence',
        };
    }

    /**
     * Return all available enum values.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
