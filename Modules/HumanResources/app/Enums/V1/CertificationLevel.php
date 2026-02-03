<?php

namespace Modules\HumanResources\Enums\V1;

use App\Traits\HasEnumTranslation;

enum CertificationLevel: string
{
    use HasEnumTranslation;

    case JUNIOR = 'junior';
    case SENIOR = 'senior';
    case EXPERT = 'expert';

    /**
     * All values for validation
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
