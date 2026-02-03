<?php

namespace Modules\Beneficiaries\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * @Enum ResidenceType
 * 
 * Categorizes the beneficiary's environment into Urban or Rural settings.
 * This classification is vital for resource allocation, as needs in rural 
 * areas often differ significantly from urban centers.
 * 
 * @method static array all() Returns a flat array of all residence values ['urban', 'rural'].
 */
enum ResidenceType: string
{
    use HasEnumTranslation;
    
    case URBAN = 'urban';
    case RURAL = 'rural';

    /**
     * Retrieve all enum values.
     * Common use case: Validation rules in FormRequests.
     * * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
