<?php

namespace Modules\CaseManagement\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * @Enum ProgressStatus
 * 
 * * Represents the qualitative assessment of a beneficiary's trajectory.
 * This Enum is the core metric for impact evaluation, allowing specialists 
 * to categorize the effectiveness of interventions over time.
 * * @method static array all() Returns a flat array of values ['improving', 'stable', 'worsening'].
 */
enum ProgressStatus: string
{
    use HasEnumTranslation;

    case IMPROVING = 'improving';
    case STABLE = 'stable';
    case WORSENING = 'worsening';


    /**
     * Retrieve all enum values.
     * Primarily used for FormRequest validation rules and database seeding.
     * * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
