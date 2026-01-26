<?php

namespace Modules\CaseManagement\Enums;

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
     * Get a human-readable label for the service direction.
     *
     * This label is intended for UI display, reports, and API responses,
     * while the enum value is used for storage and internal logic.
     *
     *  @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::MEDICAL    => 'Medical Service',
            self::LEGAL      => 'Legal Service',
            self::VOCATIONAL => 'Vocational Service',
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


