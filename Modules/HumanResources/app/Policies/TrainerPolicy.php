<?php

namespace Modules\HumanResources\Policies;

use Modules\HumanResources\Models\Trainer;
use Modules\Core\Models\User;
use Modules\HumanResources\Enums\TrainerStatus;

/**
 * Class TrainerPolicy
 *
 * Authorization rules for Trainer management.
 *
 * Core Concepts:
 * - Trainers self-register and start with PENDING status
 * - Admin approves/rejects trainers
 * - Trainer is linked to User (user_id)
 * - Provider access is scoped via:
 *   Trainer -> ActivitySession -> Activity -> provider_entity_id
 */
class TrainerPolicy
{
    /**
     * View list of trainers
     *
     * Allowed:
     * - Admin
     * - Program Manager
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'program_manager']);
    }

    /**
     * View a specific trainer
     *
     * Rules:
     * - Admin: can view all
     * - Program Manager / Community Provider:
     *   can view trainers linked to activities under same provider_entity
     * - Trainer: can view own profile
     */
    public function view(User $user, Trainer $trainer): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole(['program_manager', 'community_provider'])) {
            return $trainer->activitySessions()
                ->whereHas('activity', fn ($q) =>
                    $q->where('provider_entity_id', $user->provider_entity_id)
                )
                ->exists();
        }

        if ($user->hasRole('trainer')) {
            return $trainer->user_id === $user->id;
        }

        return false;
    }

    /**
     * Create trainer (Self-registration)
     *
     * Allowed:
     * - Any authenticated user who is NOT already a trainer
     */
    public function create(User $user): bool
    {
        return !$user->hasRole('trainer');
    }

    /**
     * Update trainer profile
     *
     * Rules:
     * - Admin: can update anytime
     * - Trainer: can update own profile ONLY if status = PENDING
     * - Program Manager:
     *   can update ONLY if:
     *   - trainer is PENDING
     *   - all sessions belong to same provider_entity
     */
    public function update(User $user, Trainer $trainer): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (
            $user->hasRole('trainer') &&
            $trainer->user_id === $user->id &&
            $trainer->status === TrainerStatus::PENDING
        ) {
            return true;
        }

        if ($user->hasRole('program_manager')) {
            if ($trainer->status !== TrainerStatus::PENDING) {
                return false;
            }

            $totalSessions = $trainer->activitySessions()->count();

            if ($totalSessions === 0) {
                return true;
            }

            $matchedSessions = $trainer->activitySessions()
                ->whereHas('activity', fn ($q) =>
                    $q->where('provider_entity_id', $user->provider_entity_id)
                )
                ->count();

            return $totalSessions === $matchedSessions;
        }

        return false;
    }

    /**
     * Delete trainer
     *
     * Rules:
     * - Admin: can delete anytime
     * - Program Manager:
     *   can delete ONLY if:
     *   - trainer is still PENDING
     *   - no future sessions under same provider_entity
     */
    public function delete(User $user, Trainer $trainer): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (
            $user->hasRole('program_manager') &&
            $trainer->status === TrainerStatus::PENDING
        ) {
            $hasFutureSessions = $trainer->activitySessions()
                ->where('date', '>=', now())
                ->whereHas('activity', fn ($q) =>
                    $q->where('provider_entity_id', $user->provider_entity_id)
                )
                ->exists();

            return !$hasFutureSessions;
        }

        return false;
    }

    /**
     * Approve trainer
     *
     * Rules:
     * - Only Admin
     * - Trainer must be in PENDING status
     *
     * This action:
     * - Changes status to APPROVED
     * - Assigns "trainer" role to related user
     */
    public function approve(User $user, Trainer $trainer): bool
    {
        return $user->hasRole('admin')
            && $trainer->status === TrainerStatus::PENDING;
    }

    /**
     * Reject trainer
     *
     * Rules:
     * - Only Admin
     * - Trainer must be in PENDING status
     */
    public function reject(User $user, Trainer $trainer): bool
    {
        return $user->hasRole('admin')
            && $trainer->status === TrainerStatus::PENDING;
    }
}
