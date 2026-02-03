<?php

namespace Modules\CaseManagement\Enums;

enum SessionType: string
{
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

    /**
     * Get label for UI / API 
     */
    public function label(): string
    {
        // Use the enum value as key in the lang file
        return __('session_types.' . $this->value);
    }

}
