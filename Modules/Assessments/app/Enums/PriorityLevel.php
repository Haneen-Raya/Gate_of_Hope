<?php

namespace Modules\Assessments\Enums;

/**
 * Enum PriorityLevel
 *
 * Defines the urgency levels for assessments and cases.
 * Used to categorize records based on their importance and response requirements.
 *
 * @package Modules\Assessments\Enums
 */
enum PriorityLevel: string
{
    /** Routine importance; standard response time. */
    case LOW = 'low';

    /** Requires attention within normal operating hours. */
    case MEDIUM = 'medium';

    /** Urgent; requires prioritized handling. */
    case HIGH = 'high';

    /** Immediate action required; highest level of urgency. */
    case CRITICAL = 'critical';

    /**
     * Retrieve all priority level values as an array of strings.
     *
     * Often used in validation rules: Rule::in(PriorityLevel::all())
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
