<?php

namespace Modules\Beneficiaries\Enums;

enum HousingTenure:string {
    case OWNED ='owned';
    case RENTED ='rented';
    case HOSTED ='hosted ';
    case INFORMAL ='informal ';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
