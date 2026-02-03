<?php

namespace Modules\Programs\Enums\V1;

/**
 * Enum ProgramStatus
 * * Managed Lifecycle of a Rehabilitation Program.
 * This Enum acts as a State Machine, defining all possible operational
 * states and the strict logic governing transitions between them.
 * * @package Modules\Programs\Enums\Program
 */
enum ProgramStatus: string
{
    /** * Initial preparation stage.
     * Program is hidden from specialists and beneficiaries.
     */
    case DRAFT = 'draft';

    /** * Live stage.
     * Program is fully operational and accepting beneficiaries.
     */
    case ACTIVE = 'active';

    /** * Fulfillment stage.
     * Program has successfully met its objectives or reached its end date.
     */
    case COMPLETED = 'completed';

    /** * Temporary halt.
     * Operational activity is paused (e.g., pending funding or administrative review).
     */
    case SUSPENDED = 'suspended';

    /** * Terminal historical stage.
     * Program is locked for editing and kept only for reporting and audit purposes.
     */
    case ARCHIVED = 'archived';

    /**
     * Retrieve a flat array of all status values.
     * * Useful for validation rules like Rule::in().
     * * @return array<int, string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Validate the transition to a new status.
     * * Implements business integrity rules:
     * - DRAFT can only go to ACTIVE or be ARCHIVED.
     * - ACTIVE can transition to COMPLETED or be SUSPENDED.
     * - SUSPENDED can be resumed (ACTIVE) or ARCHIVED.
     * - COMPLETED can only move to ARCHIVED.
     * - ARCHIVED is a terminal state (no further transitions).
     * * @param self $newStatus The target status to transition to.
     * @return bool True if the transition is logically permitted.
     */
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
