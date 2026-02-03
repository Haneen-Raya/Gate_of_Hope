<?php

namespace Modules\CaseManagement\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * @Enum PlanStatus
 * 
 * * Defines the operational lifecycle states for Case Support Plans and their Goals.
 * This Enum orchestrates the workflow progression from initial drafting to 
 * final achievement or cancellation.
 * 
 * * @method static array all() Returns a flat array of all state values ['pending', 'in_progress', 'achieved', 'cancelled'].
 */
enum PlanStatus: string
{
    use HasEnumTranslation;

    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case ACHIEVED = 'achieved';
    case CANCELLED = 'cancelled';


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
