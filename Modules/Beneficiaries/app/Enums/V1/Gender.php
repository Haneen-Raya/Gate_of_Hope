<?php

namespace Modules\Beneficiaries\Enums\V1;

/**
 * @Enum Gender
 * 
 * Defines the biological and social gender classification for beneficiaries.
 * Used for demographic reporting and targeted assistance programs.
 * 
 * @method static array all() Returns a flat array of all string values ['male', 'female'].
 */
enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    /**
     * Get a human-readable label for each gender.
     * Useful for UI displays, Exports, and Reports.
     * * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
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
