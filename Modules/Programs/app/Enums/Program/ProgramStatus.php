<?php

namespace Modules\Programs\Enums\Program;

/**
 * Enum ProgramStatus
 *
 * Defines the lifecycle stages of a rehabilitation program.
 *
 * @package Modules\Programs\Enums
 */
enum ProgramStatus: string
{
    /** Initial stage, not yet visible to specialists or beneficiaries. */
    case DRAFT = 'draft';

    /** Program is live and accepting beneficiaries. */
    case ACTIVE = 'active';

    /** Program has reached its end date or objectives. */
    case COMPLETED = 'completed';

    /** Program is temporarily on hold (e.g., funding issues). */
    case SUSPENDED = 'suspended';

    /** Program is archived for historical data and reporting. */
    case ARCHIVED = 'archived';

    /**
     * Get all status values for validation.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canTransitionTo(self $newStatus): bool
{
    return match($this) {
        self::DRAFT     => in_array($newStatus, [self::ACTIVE, self::ARCHIVED]),
        self::ACTIVE    => in_array($newStatus, [self::COMPLETED, self::SUSPENDED]),
        self::SUSPENDED => in_array($newStatus, [self::ACTIVE, self::ARCHIVED]),
        self::COMPLETED => $newStatus === self::ARCHIVED,
        self::ARCHIVED  => false,

        default => false,
    };
}
}
