<?php

namespace Modules\CaseManagement\Enums;

use App\Traits\HasEnumTranslation;

/**
 * Enum CaseStatus
 *
 * Defines the possible states a beneficiary case can have within the system.
 * Used for status tracking and filtering in BeneficiaryCase model.
 *
 * @package Modules\CaseManagement\Enums
 */
enum CaseStatus: string
{
    use HasEnumTranslation;
    /** Case is newly created and awaiting action. */
    case OPEN = 'open';

    /** Case is currently being handled by a case manager. */
    case IN_PROGRESS = 'in_progress';

    /** Case has been officially resolved or terminated. */
    case CLOSED = 'closed';

    /** Case has been forwarded to another entity or program. */
    case REFERRED = 'referred';

    /**
     * Retrieve the translated label for the current case status.
     *
     * This method fetches the translation from:
     * Modules/CaseManagement/lang/{locale}/case_status.php
     *
     * @return string The localized label.
     */
    public function label(): string
    {
        return __("casemanagement::case_status.{$this->value}");
    }

    /**
     * Get all raw string values of the enum cases.
     *
     * Useful for validation rules or population of dropdown lists.
     *
     * @return array<int, string> List of status values.
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
