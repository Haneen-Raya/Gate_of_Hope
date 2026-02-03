<?php

namespace Modules\Beneficiaries\Enums\V1;

use App\Traits\HasEnumTranslation;

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
    use HasEnumTranslation;

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
     * Retrieve all enum values.
     * Common use case: Validation rules in FormRequests.
     * * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
