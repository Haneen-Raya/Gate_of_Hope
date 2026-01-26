<?php

namespace Modules\CaseManagement\Enums;

/**
 * Enum CaseStatus
 *
 * Defines the possible states a beneficiary case can have within the system.
 * Used for status tracking and filtering in BeneficiaryCase model.
 *
 * @package Modules\CaseManagement\Enums
 */
enum CaseStatus: string
{
    /** Case is newly created and awaiting action. */
    case OPEN = 'open';

    /** Case is currently being handled by a case manager. */
    case IN_PROGRESS = 'in_progress';

    /** Case has been officially resolved or terminated. */
    case CLOSED = 'closed';

    /** Case has been forwarded to another entity or program. */
    case REFERRED = 'referred';

    /**
     * Get all raw string values of the enum cases.
     *
     * Useful for validation rules or population of dropdown lists.
     *
     * @return array<int, string> List of status values.
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
