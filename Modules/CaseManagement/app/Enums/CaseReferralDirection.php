<?php

namespace Modules\CaseManagement\Enums;

/**
 * @Enum CaseReferralDirection
 *
 * Represents the direction of a case referral within the system.
 *
 * This enum defines whether a referred service is delivered:
 * - Internally by the organization itself
 * - Externally by a partner, governmental, or third-party entity
 *
 * It is used to standardize referral workflows, reporting,
 * and service coordination across modules such as:
 * cases, referrals, services, and activities.
 *
 * @method static array all() Returns a flat array of all string values.
 */
enum CaseReferralDirection : string
{
    /* Referral for a service delivered internally by the organization or its internal departments.*/
    case INTERNAL = 'internal';

    /* Referral for a service delivered externally by a partner organization or third-party entity.*/
    case EXTERNAL = 'external';

    /**
     * Get a human-readable label for the referral direction.
     *
     * This label is intended for:
     * - UI dropdowns and badges
     * - Reports and exports
     * - API consumer readability
     *
     * The enum value is used internally for storage
     * and business logic consistency.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::INTERNAL   => 'Internal Referral',
            self::EXTERNAL   => 'External Referral',
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

