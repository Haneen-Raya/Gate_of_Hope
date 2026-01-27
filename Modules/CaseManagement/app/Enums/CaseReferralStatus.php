<?php

namespace Modules\CaseManagement\Enums;

/**
 * @Enum CaseReferralStatus
 *
 * Represents the lifecycle status of a case referral within the system.
 *
 * This enum defines the current processing stage of a referral,
 * starting from creation, through acceptance by the receiving entity,
 * and ending with service completion.
 *
 * It is a core component in managing referral workflows, follow-ups,
 * and reporting across case management and partner coordination modules.
 *
 * @method static array all() Returns a flat array of all string values.
 */
enum CaseReferralStatus : string
{
    /** The referral has been created and sent to the receiving entity, but no action has been taken yet.*/
    case REFERRED = 'referred';

    /** The receiving entity has acknowledged and accepted the referral for processing. */
    case ACCEPTED = 'accepted';

    /** The referred service has been fully delivered and the referral process has been completed. */
    case COMPLETED = 'completed';

    /** The referral has been reviewed by the receiving entity and explicitly rejected, with a documented reason. */
    case REJECTED = 'rejected';

    /** The referral has been cancelled after creation,either by the sender or due to administrative reasons,
     * before service completion.
     */
    case CANCELLED = 'cancelled';

    /**
     * Get a human-readable label for the referral status.
     *
     * These labels are intended for:
     * - UI status indicators and badges
     * - Case tracking dashboards
     * - Reports and exports
     *
     * The enum value itself is used for persistence
     * and internal business logic.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::REFERRED   => 'Referred',
            self::ACCEPTED   => 'Accepted',
            self::COMPLETED  => 'Completed',
            self::CANCELLED  => 'Cancelled',
            self::REJECTED   => 'Rejected',
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


