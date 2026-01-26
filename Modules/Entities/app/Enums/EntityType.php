<?php

namespace Modules\Entities\Enums;


/**
 * @Enum EntityType
 *
 * Defines the classification of entities within the system.
 *
 * This enum is used to distinguish between different types of entities
 * such as non-governmental organizations and government institutions.
 * It is primarily associated with the entities table.
 *
 * @method static array all() Returns a flat array of all string values.
 */
enum EntityType : string
{
    /** Non-Governmental Organization (NGO). */
    case NGO = 'ngo';

    /** Governmental entity or public institution. */
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


