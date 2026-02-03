<?php

namespace Modules\CaseManagement\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * @Enum CaseReferralUrgencyLevel
 *
 * Represents the urgency level of a case referral.
 *
 * This enum defines how time-sensitive a referral is,
 * helping prioritize case handling and service delivery.
 *
 * It is used to support workflow prioritization,
 * operational decision-making, and reporting.
 *
 * @method static array all() Returns a flat array of all string values.
 */
enum CaseReferralUrgencyLevel : string
{
    use HasEnumTranslation;

    /**
     * Normal priority referral.
     *
     * Indicates that the referral can be processed
     * within standard operational timeframes.
     */
    case NORMAL = 'normal';

    /**
     * Urgent priority referral.
     *
     * Indicates that the referral requires immediate
     * or accelerated action due to critical circumstances.
     */
    case URGENT = 'urgent';

    /**
     * Retrieve all enum values.
     * Common use case: Validation rules in FormRequests.
     * * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}

