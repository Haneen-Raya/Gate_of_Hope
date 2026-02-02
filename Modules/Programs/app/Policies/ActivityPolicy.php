<?php

namespace Modules\Programs\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;
use Modules\Programs\Models\Activity;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view a specific activity.
     *
     * Access is granted if:
     * - The user is an admin.
     * - The user belongs to the provider entity of the activity.
     * - The user is the program manager responsible for this activity.
     *
     * @param User $user The authenticated user.
     * @param Activity $activity The requested activity record.
     *
     * @return bool True if authorized, otherwise false.
     */
    public function view(User $user, Activity $activity): bool
    {
        return
            // 1. Admin has full access
            $user->hasRole('admin')

            // 2. user belongs to the provider entity of the activity.
            || ($user->hasRole('community_provider') && $activity->isProvidedByEntityOf($user))

            // 3. user is the program manager for this activity.
            || ($user->hasRole('program_manager') && $activity->isManagedBy($user));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,$beneficiaryCase): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update an existing activity.
     *
     * Access is granted if:
     * - The user is an admin.
     * - The user is the program manager of the related program.
     *
     * @param User $user The authenticated user.
     * @param Activity $activity The activity being updated.
     *
     * @return bool True if authorized, otherwise false.
     */
    public function update(User $user, Activity $activity): bool
    {
        return  $user->hasRole('admin')
                || $activity->isManagedBy($user) ;
    }

    /**
     * Determine whether the user can delete an activity.
     *
     * Access is granted if:
     * - The user is an admin.
     * - The user is the program manager responsible for this activity.
     *
     * @param User $user The authenticated user.
     * @param Activity $activity The activity being deleted.
     *
     * @return bool True if authorized, otherwise false.
     */
    public function delete(User $user, Activity $activity): bool
    {
        return  $user->hasRole('admin')
                || $activity->isManagedBy($user) ;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Activity $activity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Activity $activity): bool
    {
        return false;
    }

}
