<?php

namespace Modules\Beneficiaries\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Beneficiaries\Models\SocialBackground;
use Modules\Core\Models\User;

class SocialBackgroundPolicy
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
     * Determine whether the user can view a specific SocialBackground.
     *
     * Case 1: A beneficiary can only view their own record.
     * Case 2: Other roles must have 'social_backgrounds.read' permission.
     *
     * @param User $user The authenticated user
     * @param SocialBackground $socialBackground The social background record
     *
     * @return Response
     */
    public function view(User $user, SocialBackground $socialBackground): Response
    {
        // Case 1: Beneficiary can only view his own record
        if ($user->hasRole('beneficiary')) {
            return $user->id === $socialBackground->beneficiary->user_id
                ? Response::allow()
                : Response::deny("You can only view your own social background.");
        }

        // Case 2: Other roles must have permission
        return $user->can('social_backgrounds.read')
            ? Response::allow()
            : Response::deny("You don't have permission to view this social background.");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update a specific SocialBackground.
     *
     * Only the owner with 'social_backgrounds.update.self' permission can update.
     *
     * @param User $user The authenticated user
     * @param SocialBackground $socialBackground The social background record
     *
     * @return Response
     */
    public function update(User $user, SocialBackground $socialBackground):Response
    {
        return ($user->can('social_backgrounds.update.self')
                && $user->id === $socialBackground->beneficiary->user_id)
                ? Response::allow()
                : Response::deny("You can only update your own social background.");
    }

    /**
     * Determine whether the user can delete a specific SocialBackground.
     *
     * Only the owner with 'social_backgrounds.delete.self' permission can delete.
     *
     * @param User $user The authenticated user
     * @param SocialBackground $socialBackground The social background record
     * @return Response
     */
    public function delete(User $user, SocialBackground $socialBackground): Response
    {
        return ($user->can('social_backgrounds.delete.self')
                && $user->id === $socialBackground->beneficiary->user_id)
                ? Response::allow()
                : Response::deny("You can only delete your own social background.");
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SocialBackground $socialBackground): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SocialBackground $socialBackground): bool
    {
        return false;
    }
}
