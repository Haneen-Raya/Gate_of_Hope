<?php

namespace Modules\Programs\Enums\V1\Activity;

use App\Traits\HasEnumTranslation;

/**
 * @Enum ActivityType
 *
 * Represents the classification of program activities delivered
 * to beneficiaries within the Gate of Hope system.
 *
 * Activity types are used for:
 * - Filtering and reporting
 * - Standardizing activity categories
 * - Supporting program evaluation and monitoring
 *
 * @method static array all() Returns all enum values as strings.
 */
enum ActivityType: string
{
    use HasEnumTranslation;

    /** Community awareness and educational sessions. */
    case AWARENESS = 'awareness';

    /** Training workshops and capacity-building activities. */
    case TRAINING = 'training';

    /** Psychosocial support and wellbeing-focused activities. */
    case PSYCHOSOCIAL = 'psychosocial';

    /** Recreational and social inclusion activities. */
    case RECREATIONAL = 'recreational';

    /** Community engagement and outreach initiatives. */
    case COMMUNITY = 'community';

    /** Educational support programs. */
    case EDUCATIONAL = 'educational';

    /** Vocational training and livelihood support. */
    case VOCATIONAL = 'vocational';

    /**
     * Return all available enum values.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
