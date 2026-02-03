<?php

namespace Modules\CaseManagement\Enums\V1;

use App\Traits\HasEnumTranslation;

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
    use HasEnumTranslation;

    /* Referral for a service delivered internally by the organization or its internal departments.*/
    case INTERNAL = 'internal';

    /* Referral for a service delivered externally by a partner organization or third-party entity.*/
    case EXTERNAL = 'external';


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

