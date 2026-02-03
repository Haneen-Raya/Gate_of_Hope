<?php

namespace Modules\HumanResources\Enums\V1;

use App\Traits\HasEnumTranslation;

/**
 * Enum TrainerStatus
 *
 * Represents the lifecycle status of a Trainer account.
 *
 * Usage:
 * - PENDING   → Trainer applied and waiting for admin approval
 * - APPROVED  → Trainer approved and active
 * - REJECTED  → Trainer application rejected
 *
 * Common Use Cases:
 * - Database column casting
 * - Validation rules in FormRequests
 * - Business logic checks
 * - UI status labels
 */
enum TrainerStatus: string
{
    use HasEnumTranslation;

    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get all enum values as a flat array.
     *
     * Commonly used in validation rules:
     * Rule::in(TrainerStatus::all())
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if trainer is approved.
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if trainer is pending approval.
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if trainer is rejected.
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
}
