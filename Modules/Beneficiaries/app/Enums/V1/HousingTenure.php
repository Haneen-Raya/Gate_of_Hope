<?php

namespace Modules\Beneficiaries\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * Class HousingTenure
 *
 * Represents the type of housing tenure for a beneficiary's residence.
 *
 * Each enum case reflects the legal/occupancy status of the housing:
 * - OWNED: The property is owned by the beneficiary or family.
 * - RENTED: The property is rented from another entity.
 * - HOSTED: The property is provided or hosted by a relative/friend.
 * - INFORMAL: The property is informal or irregularly occupied (e.g., squatting, unregistered).
 *
 * Usage:
 * - HousingTenure::OWNED->value
 * - HousingTenure::all()
 * - $enumCase->label()
 *
 * @package Modules\Beneficiaries\Enums\V1
 */
enum HousingTenure: string
{
    use HasEnumTranslation;

    // The property is owned by the beneficiary or family.
    case OWNED = 'owned';

    // The property is rented from another entity.
    case RENTED = 'rented';

    // The property is provided or hosted by a relative or friend.
    case HOSTED = 'hosted';

    // The property is informal or irregularly occupied (e.g., squatting, unregistered).
    case INFORMAL = 'informal';

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
