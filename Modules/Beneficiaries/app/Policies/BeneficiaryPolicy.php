<?php

namespace Modules\Beneficiaries\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\Core\Models\User;

class BeneficiaryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('beneficiaries.read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Beneficiary $beneficiary): bool
    {
        return $user->can('beneficiaries.read') || $beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('beneficiaries.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Beneficiary $beneficiary): bool
    {
        return $user->can('beneficiaries.update') || $beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Beneficiary $beneficiary): bool
    {
        return $user->can('beneficiaries.delete') || $user->id == $beneficiary->user_id;
    }
}
