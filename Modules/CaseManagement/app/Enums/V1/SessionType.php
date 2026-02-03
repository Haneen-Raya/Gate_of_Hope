<?php

namespace Modules\CaseManagement\Enums\V1;

use App\Traits\HasEnumTranslation;

enum SessionType: string
{
    use HasEnumTranslation;
    case INDIVIDUAL = 'individual';
    case GROUP = 'group';
    case FAMILY = 'family';
    case FOLLOW_UP = 'follow_up';
    case ASSESSMENT = 'assessment';

    /**
     * Get all values (useful for validation)
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}
