<?php

namespace Modules\CaseManagement\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * @Enum CaseReferralType
 *
 * Represents the type of a case referral.
 *
 * This enum defines the type of service being  referred,
 * such as medical, legal, or vocational support.
 *
 * It is used to classify referrals for reporting, workflow handling,
 * and routing to the appropriate service providers.
 *
 * @method static array all() Returns a flat array of all string values.
 */
enum CaseReferralType : string
{
    use HasEnumTranslation;
    
    /** Medical-related referral.
     *
     * Includes health services such as medical consultations,
     * treatments, medications, rehabilitation, or specialized care.
     */
    case MEDICAL = 'medical';

    /** Legal-related referral.
     *
     * Includes legal aid services such as legal consultation,
     * documentation support, representation, or rights advocacy.
     */
    case LEGAL = 'legal';

    /*** Vocational-related referral.
     *
     * Includes services related to skills training, employment support,
     * income-generating activities, or professional development.
     */
    case VOCATIONAL = 'vocational';

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


