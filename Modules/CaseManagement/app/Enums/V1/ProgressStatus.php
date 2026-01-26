<?php

namespace Modules\CaseManagement\Enums\V1;

/**
 * @Enum ProgressStatus
 * 
 * * Represents the qualitative assessment of a beneficiary's trajectory.
 * This Enum is the core metric for impact evaluation, allowing specialists 
 * to categorize the effectiveness of interventions over time.
 * * @method static array all() Returns a flat array of values ['improving', 'stable', 'worsening'].
 */
enum ProgressStatus: string
{
    case IMPROVING = 'improving';
    case STABLE = 'stable';
    case WORSENING = 'worsening';

    /**
     * Get a human-readable label for each status.
     * Essential for UI consistency, PDF exports, and stakeholder reporting.
     * * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::IMPROVING     => 'Improving',
            self::STABLE => 'Stable',
            self::WORSENING    => 'Worsening',
        };
    }

    /**
     * Retrieve all enum values.
     * Primarily used for FormRequest validation rules and database seeding.
     * * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
