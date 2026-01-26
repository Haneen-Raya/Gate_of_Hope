<?php

namespace Modules\CaseManagement\Enums;

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
     * Get a human-readable label for the service direction.
     *
     * This label is intended for UI display, reports, and API responses,
     * while the enum value is used for storage and internal logic.
     *
     *  @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NORMAL   => 'Normal Priority',
            self::URGENT   =>  'Urgent Priority',
        };
    }

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

