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
     * Determine whether the user can view a specific program funding record.
     *
     * Access is granted if:
     * - The user is an admin.
     * - The user is the donor who provided this funding.
     * - The user is the program manager responsible for the related program.
     *
     * @param User $user The authenticated user.
     * @param ProgramFunding $programFunding The requested funding record.
     *
     * @return bool True if the user is authorized, otherwise false.
     */
    public function view(User $user, ProgramFunding $programFunding): bool
    {
        return
            // 1. Admin has full access
            $user->hasRole('admin')

            // 2. donor of the program funding
            || ($user->hasRole('donor') && $programFunding->isDonoredBy($user))

            // 3.
            || ($user->hasRole('program_manager') && $programFunding->isProgramManagedBy($user));
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
