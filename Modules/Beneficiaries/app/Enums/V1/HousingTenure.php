<?php

namespace Modules\Beneficiaries\Enums\V1;

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
    // The property is owned by the beneficiary or family.
    case OWNED = 'owned';

    // The property is rented from another entity.
    case RENTED = 'rented';

    // The property is provided or hosted by a relative or friend.
    case HOSTED = 'hosted';

    // The property is informal or irregularly occupied (e.g., squatting, unregistered).
    case INFORMAL = 'informal';

    /**
     * Return the human-readable label of the current enum case.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::OWNED => 'Owned',
            self::RENTED => 'Rented',
            self::HOSTED => 'Hosted',
            self::INFORMAL => 'Informal',
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
