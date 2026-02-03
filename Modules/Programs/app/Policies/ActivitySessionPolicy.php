<?php

namespace Modules\Programs\Policies;

use Modules\Programs\Models\ActivitySession;
use Modules\Core\Models\User;
use Modules\Programs\Enums\V1\ActivitySessionStatus;

/**
 * Class ActivitySessionPolicy
 *
 * Defines authorization rules for ActivitySession actions.
 * Handles role-based permissions for admin, program managers, trainers,
 * and other user roles.
 */
class ActivitySessionPolicy
{
    /**
     * Determine if the user can view all activity sessions.
     *
     * Only users with roles 'admin' or 'program_manager' are allowed.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'program_manager']);
    }

    /**
     * Determine if the user can view a single activity session.
     *
     * Admins can view all sessions.
     * Program Managers can view sessions within their provider entity.
     * Trainers can view only their own sessions.
     *
     * @param User $user
     * @param ActivitySession $session
     * @return bool
     */
    public function view(User $user, ActivitySession $session): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('program_manager')) {
            return $session->activity->provider_entity_id === $user->provider_entity_id;
        }

        if ($user->hasRole('trainer')) {
            return $session->trainer_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create a new activity session.
     *
     * Only Admin and Program Manager roles can create sessions.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'program_manager']);
    }

    /**
     * Determine if the user can update an activity session.
     *
     * Restrictions:
     * - Completed or cancelled sessions cannot be updated.
     *
     * Admins: can update any session.
     * Program Managers: can update sessions in their provider entity.
     * Trainers: can update only their own scheduled sessions.
     *
     * @param User $user
     * @param ActivitySession $session
     * @return bool
     */
    public function update(User $user, ActivitySession $session): bool
    {
        if (in_array($session->status, [
            ActivitySessionStatus::COMPLETED,
            ActivitySessionStatus::CANCELLED
        ])) {
            return false;
        }

        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('program_manager')) {
            return $session->activity->provider_entity_id === $user->provider_entity_id;
        }

        if ($user->hasRole('trainer')) {
            return $session->trainer_id === $user->id &&
                   $session->status === ActivitySessionStatus::SCHEDULED;
        }

        return false;
    }

    /**
     * Determine if the user can delete an activity session.
     *
     * Restrictions:
     * - Past sessions cannot be deleted.
     *
     * Admins: can delete any session.
     * Program Managers: can delete sessions in their provider entity.
     *
     * @param User $user
     * @param ActivitySession $session
     * @return bool
     */
    public function delete(User $user, ActivitySession $session): bool
    {
        if ($session->session_date < today()) return false;

        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('program_manager')) {
            return $session->activity->provider_entity_id === $user->provider_entity_id;
        }

        return false;
    }

    /**
     * Determine if the user can mark an activity session as completed.
     *
     * Only sessions with status 'ONGOING' can be completed.
     * Allowed roles: Admin and Program Manager.
     *
     * @param User $user
     * @param ActivitySession $session
     * @return bool
     */
    public function complete(User $user, ActivitySession $session): bool
    {
        if ($session->status !== ActivitySessionStatus::ONGOING) return false;

        return $user->hasRole(['admin', 'program_manager']);
    }

    /**
     * Determine if the user can cancel an activity session.
     *
     * Restrictions:
     * - Completed or cancelled sessions cannot be cancelled again.
     * Allowed roles: Admin and Program Manager.
     *
     * @param User $user
     * @param ActivitySession $session
     * @return bool
     */
    public function cancel(User $user, ActivitySession $session): bool
    {
        if (in_array($session->status, [
            ActivitySessionStatus::COMPLETED,
            ActivitySessionStatus::CANCELLED
        ])) return false;

        return $user->hasRole(['admin', 'program_manager']);
    }

    /**
     * Determine if the user can view upcoming sessions for a trainer.
     *
     * Rules:
     * - The trainer themselves can view their own upcoming sessions.
     * - Community Providers and Admins can view any trainer's sessions.
     * - Users with the permission 'activity_sessions.view_all_trainers' can also view.
     *
     * @param User $user
     * @param int $trainerId
     * @return bool
     */
    public function viewUpcomingForTrainer(User $user, int $trainerId): bool
    {
        if ($user->id === $trainerId) {
            return true;
        }

        return $user->hasRole('community_provider')
            || $user->can('activity_sessions.view_all_trainers');
    }
}
