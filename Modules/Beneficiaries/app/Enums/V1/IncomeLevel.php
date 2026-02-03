<?php

namespace Modules\Beneficiaries\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * Class IncomeLevel
 *
 * Represents the income level of a beneficiary or household.
 *
 * Each enum case reflects the general financial status:
 * - NONE: No income at all.
 * - LOW: Low income, barely meets basic needs.
 * - MEDIUM: Moderate income, can cover essential needs comfortably.
 * - HIGH: High income, above average living standard.
 *
 * Usage:
 * - IncomeLevel::LOW->value
 * - IncomeLevel::all()
 * - $enumCase->label()
 *
 * @package Modules\Beneficiaries\Enums\V1
 */
enum IncomeLevel: string
{
    use HasEnumTranslation;

    // No income at all.
    case NONE = 'none';

    // Low income, barely meets basic needs.
    case LOW = 'low';

    // Moderate income, can cover essential needs comfortably.
    case MEDIUM = 'medium';

    // High income, above average living standard.
    case HIGH = 'high';


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
