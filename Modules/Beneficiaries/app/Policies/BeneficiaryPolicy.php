<?php

namespace Modules\Beneficiaries\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\Core\Models\User;

/**
 * Class BeneficiaryPolicy
 *
 * Manages authorization logic for the Beneficiary model.
 * * SECURITY STRATEGY:
 * This policy implements a hybrid authorization model:
 * 1. Role-Based Access Control (RBAC): Checks for global permissions (e.g., beneficiaries.read).
 * 2. Ownership-Based Access Control (OBAC): Grants access if the authenticated user is the
 * assigned owner of the beneficiary record ($beneficiary->user_id).
 *
 * @package Modules\Beneficiaries\Policies
 */
class BeneficiaryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Determine whether the user can view the list of beneficiaries.
     * Required permission: 'beneficiaries.read'
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('beneficiaries.read');
    }

    /**
     * Determine whether the user can view a specific beneficiary profile.
     * Access granted if:
     * - User has 'beneficiaries.read' permission.
     * - OR User is the creator/owner of the beneficiary record.
     *
     * @param User $user The authenticated user.
     * @param Beneficiary $beneficiary The beneficiary instance.
     * @return bool
     */
    public function view(User $user, Beneficiary $beneficiary): bool
    {
        return $user->can('beneficiaries.read') || $beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the user can create new beneficiary records.
     * Required permission: 'beneficiaries.create'
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('beneficiaries.create');
    }

    /**
     * Determine whether the user can update a specific beneficiary record.
     * Access granted if:
     * - User has 'beneficiaries.update' permission.
     * - OR User is the owner/manager of the record.
     *
     * @param User $user The authenticated user.
     * @param Beneficiary $beneficiary The beneficiary instance.
     * @return bool
     */
    public function update(User $user, Beneficiary $beneficiary): bool
    {
        return $user->can('beneficiaries.update') || $beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete a beneficiary record.
     * Access granted if:
     * - User has 'beneficiaries.delete' permission.
     * - OR User is the owner of the record.
     *
     * @param User $user The authenticated user.
     * @param Beneficiary $beneficiary The beneficiary instance.
     * @return bool
     */
    public function delete(User $user, Beneficiary $beneficiary): bool
    {
        return $user->can('beneficiaries.delete') || $user->id == $beneficiary->user_id;
    }
}
