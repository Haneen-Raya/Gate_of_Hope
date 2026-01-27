<?php

namespace Modules\HumanResources\Policies;

use Modules\HumanResources\Models\Trainer;
use Modules\Core\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Class TrainerPolicy
 *
 * Handles authorization logic for Trainers.
 * Determines which users can view, create, update, delete, restore, or force delete trainers
 * based on their roles and the provider_entity relationships.
 *
 * Roles & Permissions:
 * - Admin: full access to all trainers.
 * - Program Manager: can view/update/delete trainers only if sessions belong to their provider_entity.
 * - Community Provider: can view trainers linked to their activities.
 * - Trainer: can view their own profile.
 *
 * Relationships Considered:
 * Trainer -> ActivitySession -> Activity -> provider_entity_id
 *
 * @package Modules\HumanResources\Policies
 */
class TrainerPolicy
{
    /**
     * Determine whether the user can view any trainers.
     *
     * @param User $user
     * @return bool True if the user can view a list of trainers
     */
    public function viewAny(User $user): bool
    {
        // Admin أو Program Manager يمكنهم رؤية قائمة Trainers
        return $user->hasRole(['admin', 'program_manager']);
    }

    /**
     * Determine whether the user can view a specific trainer.
     *
     * Rules:
     * - Admin: can view any trainer
     * - Program Manager / Community Provider: can view only if trainer has sessions linked to their provider_entity
     * - Trainer: can view their own profile
     *
     * @param User $user
     * @param Trainer $trainer
     * @return bool
     */
    public function view(User $user, Trainer $trainer): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole(['program_manager', 'community_provider'])) {
            return $trainer->activitySessions()
                ->whereHas('activity', function ($q) use ($user) {
                    $q->where('provider_entity_id', $user->provider_entity_id);
                })
                ->exists();
        }

        if ($user->hasRole('trainer')) {
            return $trainer->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create a new trainer.
     *
     * Rules:
     * - Only Admin can create a trainer
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can update a trainer.
     *
     * Rules:
     * - Admin: can update any trainer
     * - Program Manager: can update only if all trainer sessions belong to their provider_entity
     * - Trainer: cannot update
     *
     * @param User $user
     * @param Trainer $trainer
     * @return bool
     */
    public function update(User $user, Trainer $trainer): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('program_manager')) {
            $totalSessions = $trainer->activitySessions()->count();
            if ($totalSessions === 0) {
                return true; // can update trainers without sessions
            }

            $matchedSessions = $trainer->activitySessions()
                ->whereHas('activity', fn($q) => $q->where('provider_entity_id', $user->provider_entity_id))
                ->count();

            return $totalSessions === $matchedSessions;
        }

        return false;
    }

    /**
     * Determine whether the user can delete a trainer.
     *
     * Rules:
     * - Admin: can delete any trainer
     * - Program Manager: cannot delete if trainer has active/future sessions within their provider_entity
     *
     * @param User $user
     * @param Trainer $trainer
     * @return bool
     */
    public function delete(User $user, Trainer $trainer): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('program_manager')) {
            $hasActiveSessions = $trainer->activitySessions()
                ->where('date', '>=', now())
                ->whereHas('activity', fn($q) => $q->where('provider_entity_id', $user->provider_entity_id))
                ->exists();

            return !$hasActiveSessions;
        }

        return false;
    }
}
