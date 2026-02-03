<?php

namespace Modules\HumanResources\Enums;

/**
 * Enum CertificationLevel
 * * Defines the standardized levels of professional certification within the
 * Human Resources module. This enum is used to categorize employee expertise,
 * influence payroll logic, and filter training requirements.
 * * @package Modules\HumanResources\Enums
 */
enum CertificationLevel: string
{
    /** * Represents entry-level proficiency.
     * Usually associated with 0-2 years of experience.
     */
    case JUNIOR = 'junior';

    /** * Represents advanced proficiency.
     * Usually associated with 3-7 years of experience and specialized skills.
     */
    case SENIOR = 'senior';

    /** * Represents mastery and leadership.
     * Reserved for individuals with significant industry impact and high-level skills.
     */
    case EXPERT = 'expert';

    /**
     * Get a human-readable label for the certification level.
     * Useful for front-end display, PDF reports, and localized UI elements.
     * * @return string Descriptive English label.
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
     * Retrieve all possible enum values as a flat array.
     * Primarily used in FormRequest validation rules: Rule::in(CertificationLevel::all()).
     * * @return array<int, string> List of raw string values ['junior', 'senior', 'expert'].
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
