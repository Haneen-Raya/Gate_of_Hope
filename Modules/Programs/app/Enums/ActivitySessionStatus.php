<?php

namespace Modules\Programs\Enums;

/**
 * Enum ActivitySessionStatus
 *
 * Represents the possible statuses of an Activity Session and their lifecycle rules.
 *
 * Lifecycle:
 * DRAFT → SCHEDULED → ONGOING → COMPLETED
 *              ↘︎ CANCELLED
 *
 * Rules:
 * - Cannot modify time/location after ONGOING.
 * - Cannot cancel after COMPLETED.
 * - Trainer cannot be changed after SCHEDULED.
 */
enum ActivitySessionStatus: string
{
    /**
     * Draft session
     * - Initial state before scheduling
     * - Fully editable
     */
    case DRAFT = 'draft';

    /**
     * Scheduled session
     * - Confirmed date/time/location
     * - Trainer is fixed
     * - Can still be cancelled
     */
    case SCHEDULED = 'scheduled';

    /**
     * Ongoing session
     * - Currently happening
     * - Cannot change time/location
     * - Can be completed but not cancelled
     */
    case ONGOING = 'ongoing';

    /**
     * Completed session
     * - Finished successfully
     * - Cannot modify or cancel
     */
    case COMPLETED = 'completed';

    /**
     * Cancelled session
     * - Cancelled before completion
     * - Cannot be resumed or modified
     */
    case CANCELLED = 'cancelled';
}
