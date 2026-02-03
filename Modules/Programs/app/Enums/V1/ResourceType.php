<?php

namespace Modules\Programs\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * Enum ResourceType
 * * Defines the supported categories for program resources.
 * This Enum centralizes resource classification and provides localized labels
 * for consistent usage across the API and frontend.
 * * @package Modules\Programs\Enums\V1
 */
enum ResourceType: string
{
    use HasEnumTranslation;
    /**
     * Educational materials like books, notebooks, or training guides.
     */
    case EDUCATIONAL = 'educational';

    /**
     * Logistical services such as transportation or catering.
     */
    case LOGISTICS = 'logistics';

    /**
     * Physical equipment like projectors, laptops, or furniture.
     */
    case EQUIPMENT = 'equipment';

    /**
     * Support kits distributed to participants.
     */
    case KITS = 'kits';

    /**
     * Venue-related expenses like hall rentals or maintenance.
     */
    case VENUE = 'venue';

    /**
     * Get the human-readable Arabic label for the resource type.
     * * This is primarily used by the 'type_label' accessor in the ProgramResource model.
     * * @return string Arabic translation of the enum case.
     */
    public function label(): string
    {
        return match($this) {
            self::EDUCATIONAL => 'مواد تعليمية',
            self::LOGISTICS => 'خدمات لوجستية',
            self::EQUIPMENT => 'تجهيزات ومعدات',
            self::KITS => 'حقائب دعم',
            self::VENUE => 'تأمين قاعات',
        };
    }

    /**
     * Retrieve all available raw string values of the enum.
     * * Useful for validation rules or populating dropdown lists in the frontend.
     * * @return array<int, string> List of all enum values.
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
