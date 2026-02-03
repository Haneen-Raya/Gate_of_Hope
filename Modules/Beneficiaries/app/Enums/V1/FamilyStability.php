<?php

namespace Modules\Beneficiaries\Enums\V1;

/**
 * Class FamilyStability
 *
 * Represents the stability of a family in the Beneficiaries module.
 *
 * Each enum case reflects the family's level of stability:
 * - STABLE: The family environment is stable and well-functioning.
 * - PARTIALLY_UNSTABLE: The family shows some instability or challenges.
 * - UNSTABLE: The family is unstable, with significant issues affecting well-being.
 *
 * @package Modules\Beneficiaries\Enums\V1
 */
enum FamilyStability: string
{
    //The family environment is stable and well-functioning.
    case STABLE = 'stable';

    // The family shows some instability or challenges.
    case PARTIALLY_UNSTABLE = 'partially_unstable';

    // The family is unstable, with significant issues affecting well-being.
    case UNSTABLE = 'unstable';

    /* Return the human-readable label of the current enum case.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::STABLE => 'Stable',
            self::PARTIALLY_UNSTABLE => 'Partially Unstable',
            self::UNSTABLE => 'Unstable',
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
