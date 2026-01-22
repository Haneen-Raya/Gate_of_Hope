<?php

namespace Modules\Beneficiaries\Enums;

enum LivingStandard :string {
    case VERY_POOR ='very_poor';
    case POOR ='poor';
    case ACCEPTABLE ='acceptable';
    case GOOD ='good';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
