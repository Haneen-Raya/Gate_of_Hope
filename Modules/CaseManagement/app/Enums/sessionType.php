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
        return match ($this) {
            self::INDIVIDUAL => 'Individual Session',
            self::GROUP => 'Group Session',
            self::FAMILY => 'Family Session',
            self::FOLLOW_UP => 'Follow-up Session',
            self::ASSESSMENT => 'Assessment Session',
        };
    }
}
