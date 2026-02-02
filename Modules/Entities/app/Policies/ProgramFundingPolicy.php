<?php

namespace Modules\Entities\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;
use Modules\Entities\Models\ProgramFunding;

class ProgramFundingPolicy
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
     * Determine whether the user can view the given program funding.
     *
     * Access is granted if:
     * - User is an admin
     * - User is the donor of the program funding
     *
     * @param User $user
     * @param ProgramFunding $programFunding
     *
     * @return bool
     */
    public function view(User $user, ProgramFunding $programFunding): bool
    {
        return
            // 1. Admin has full access
            $user->hasRole('admin')

            // 2. donor of the program funding
            || ($user->hasRole('donor') && $programFunding->isDonoredBy($user));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,$beneficiaryCase): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update models.
     */
    public function update(User $user, ProgramFunding $programFunding): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete models.
     */
    public function delete(User $user, ProgramFunding $programFunding): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProgramFunding $programFunding): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProgramFunding $programFunding): bool
    {
        return false;
    }


}
