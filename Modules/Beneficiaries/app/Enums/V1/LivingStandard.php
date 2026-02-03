<?php

namespace Modules\Beneficiaries\Enums\V1;

/**
 * Class LivingStandard
 *
 * Represents the living standard of a beneficiary or household.
 *
 * Each enum case reflects the general quality of life:
 * - VERY_POOR: Extremely low standard of living, lacking basic necessities.
 * - POOR: Low standard, struggles to meet basic needs.
 * - ACCEPTABLE: Moderate standard, basic needs are met comfortably.
 * - GOOD: Above average living standard, comfortable lifestyle.
 *
 * Usage:
 * - LivingStandard::POOR->value
 * - LivingStandard::all()
 * - $enumCase->label()
 *
 * @package Modules\Beneficiaries\Enums\V1
 */
enum LivingStandard: string
{
    // Extremely low standard of living, lacking basic necessities.
    case VERY_POOR = 'very_poor';

    // Low standard of living, struggles to meet basic needs.
    case POOR = 'poor';

    // Moderate standard of living, basic needs are met comfortably.
    case ACCEPTABLE = 'acceptable';

    // Above average living standard, comfortable lifestyle.
    case GOOD = 'good';

    /**
     * Return the human-readable label of the current enum case.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::VERY_POOR => 'Very Poor',
            self::POOR => 'Poor',
            self::ACCEPTABLE => 'Acceptable',
            self::GOOD => 'Good',
        };
    }
    
    /**
     * Retrieve all enum values.
     * Common use case: Validation rules in FormRequests.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
