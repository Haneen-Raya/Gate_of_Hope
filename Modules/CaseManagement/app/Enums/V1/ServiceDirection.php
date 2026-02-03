<?php

namespace Modules\CaseManagement\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * @Enum ServiceDirection
 *
 * Represents the direction of a service delivery within the system.
 * It defines whether a service is provided internally by the organization
 * or externally by a partner or third-party entity.
 *
 * This enum is commonly used in referrals, services, and activities
 * to standardize service direction handling across the application.
 *
 * @method static array all() Returns a flat array of all string values.
 */
enum ServiceDirection : string
{
    use HasEnumTranslation;

    /** Service provided internally by the organization.*/
    case INTERNAL = 'internal';

    /** Service provided externally by a partner or third-party entity. */
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

