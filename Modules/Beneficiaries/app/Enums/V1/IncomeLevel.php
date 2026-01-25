<?php

namespace Modules\Beneficiaries\Enums\V1;

enum IncomeLevel: string
{
    case NONE = 'none';
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
