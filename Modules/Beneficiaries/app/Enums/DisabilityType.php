<?php

namespace Modules\Beneficiaries\Enums;

/**
 * @Enum DisabilityType
 * 
 * Defines the standardized classifications of disabilities for beneficiaries
 * as per international humanitarian reporting standards.
 * 
 * @method static array all() Returns a flat array of all string values.
 */
enum DisabilityType: string
{
    /** Beneficiaries with partial or total loss of sight. */
    case VISUAL = 'visual';

    /** Beneficiaries with partial or total loss of hearing. */
    case HEARING = 'hearing';

    /** Physical impairments affecting mobility or manual dexterity. */
    case PHYSICAL = 'physical';

    /** Limitations in cognitive functioning and skills like communication. */
    case INTELLECTUAL = 'intellectual';

    /** Difficulty in producing speech sounds or language disorders. */
    case SPEECH = 'speech';

    /** Chronic mental health conditions affecting daily life functioning. */
    case MENTAL = 'mental';

    /** Developmental disorders including autism spectrum conditions. */
    case AUTISM = 'autism';

    /** Presence of two or more distinct primary impairments. */
    case MULTIPLE = 'multiple';

    /** Beneficiaries with no reported or identified disabilities. */
    case NONE = 'none';

    /**
     * Get a human-readable and standardized label for each disability type.
     * * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VISUAL       => 'Visual Impairment',
            self::HEARING      => 'Hearing Impairment',
            self::PHYSICAL     => 'Physical Disability',
            self::INTELLECTUAL => 'Intellectual Disability',
            self::SPEECH       => 'Speech and Language Disorder',
            self::MENTAL       => 'Mental Health Condition',
            self::AUTISM       => 'Autism Spectrum Disorder',
            self::MULTIPLE     => 'Multiple Disabilities',
            self::NONE         => 'No Disability',
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
