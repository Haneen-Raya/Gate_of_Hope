<?php

namespace Modules\Beneficiaries\Enums;

/**
 * @Enum ResidenceType
 * 
 * Categorizes the beneficiary's environment into Urban or Rural settings.
 * This classification is vital for resource allocation, as needs in rural 
 * areas often differ significantly from urban centers.
 * 
 * @method static array all() Returns a flat array of all residence values ['urban', 'rural'].
 */
enum ResidenceType: string
{
    case URBAN = 'urban';
    case RURAL = 'rural';

    /**
     * Get a human-readable title for the residence type.
     * * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::URBAN => 'Urban Area',
            self::RURAL => 'Rural Area',
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
