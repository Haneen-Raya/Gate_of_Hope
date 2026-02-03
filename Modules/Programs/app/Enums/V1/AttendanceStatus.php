<?php

namespace Modules\Programs\Enums\V1;

use App\Traits\HasEnumTranslation;

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
    use HasEnumTranslation;

    /** Beneficiary attended the session successfully. */
    case ATTENDED = 'attended';

    /** Beneficiary did not attend without excuse. */
    case ABSENT = 'absent';

    /** Beneficiary absence was excused with valid reason. */
    case EXCUSED = 'excused';

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
