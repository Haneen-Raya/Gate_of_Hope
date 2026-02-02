<?php

namespace Modules\HumanResources\Enums;

enum CertificationLevel: string
{
    case JUNIOR = 'junior';
    case SENIOR = 'senior';
    case EXPERT = 'expert';

    /**
     * Get human readable label
     */
    public function label(): string
    {
        return match($this) {
            self::JUNIOR => 'Junior',
            self::SENIOR => 'Senior',
            self::EXPERT => 'Expert',
        };
    }

    /**
     * All values for validation
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
