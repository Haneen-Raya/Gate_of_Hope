<?php

namespace Modules\Entities\Enums;


/**
 *
 *
 */
enum EntityType : string
{
    /** Service provided internally by the organization.*/
    case NGO = 'ngo';

    /** Service provided externally by a partner or third-party entity. */
    case GOVERNMENT = 'government';

    /**
     * Get a human-readable label for the entity type.
     *
     * This label is intended for UI display, reports, and API responses,
     * while the enum value is used for storage and internal logic.
     *
     *  @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NGO         => 'Ngo type',
            self::GOVERNMENT  => 'Government type',
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


