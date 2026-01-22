<?php

namespace Modules\Assessments\Enums;

enum PriorityLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
