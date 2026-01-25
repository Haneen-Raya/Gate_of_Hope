<?php

namespace Modules\CaseManagement\Enums;

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
    /** Service provided internally by the organization.*/
    case INTERNAL = 'internal';

    /** Service provided externally by a partner or third-party entity. */
    case EXTERNAL = 'external';

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
            self::INTERNAL   => 'Internal Service',
            self::EXTERNAL   => 'External Service',
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

