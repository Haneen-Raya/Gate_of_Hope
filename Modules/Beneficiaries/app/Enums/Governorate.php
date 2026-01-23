<?php

namespace Modules\Beneficiaries\Enums;

/**
 * @Enum Governorate
 * 
 * Defines the 14 official administrative divisions (Governorates) of Syria.
 * This Enum is used for geographical distribution analysis and regional 
 * targeting of humanitarian aid.
 * 
 * @method static array all() Returns a flat array of all governorate slugs.
 */
enum Governorate: string
{
    case ALEPPO = 'aleppo';
    case RAQQA = 'raqqa';
    case SUWAYDA = 'suwayda';
    case DAMASCUS = 'damascus';
    case DARAA = 'daraa';
    case DEIR_EZ_ZOR = 'deir_ez_zor';
    case HAMA = 'hama';
    case AL_HASAKAH = 'al_hasakah';
    case HOMS = 'homs';
    case IDLIB = 'idlib';
    case LATAKIA = 'latakia';
    case QUNEITRA = 'quneitra';
    case RURAL_DAMASCUS = 'rural_damascus';
    case TARTUS = 'tartus';

    /**
     * Get the human-readable English name for the governorate.
     * * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ALEPPO         => 'Aleppo',
            self::RAQQA          => 'Raqqa',
            self::SUWAYDA        => 'Suwayda',
            self::DAMASCUS       => 'Damascus',
            self::DARAA          => 'Daraa',
            self::DEIR_EZ_ZOR    => 'Deir ez-Zor',
            self::HAMA           => 'Hama',
            self::AL_HASAKAH     => 'Al-Hasakah',
            self::HOMS           => 'Homs',
            self::IDLIB          => 'Idlib',
            self::LATAKIA        => 'Latakia',
            self::QUNEITRA       => 'Quneitra',
            self::RURAL_DAMASCUS => 'Rural Damascus',
            self::TARTUS         => 'Tartus',
        };
    }

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
