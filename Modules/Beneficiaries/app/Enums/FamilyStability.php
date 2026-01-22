<?php

namespace Modules\Beneficiaries\Enums;

enum FamilyStability :string {
    case STABLE ='stable';
    case PARTIALLY_UNSTABLE ='partially_unstable';
    case UNSTABLE ='unstable';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
