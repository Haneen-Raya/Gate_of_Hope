<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CaseSupportPlan;
use Modules\Core\Models\User;

/**
 * Class CaseSupportPlanPolicy
 *
 * Manages access to the strategic Support Plans.
 * Ensures that sensitive intervention strategies are only accessible
 * to authorized staff and the owner of the beneficiary profile.
 *
 * @package Modules\CaseManagement\Policies
 */
class CaseSupportPlanPolicy
{
    use HandlesAuthorization;

    public function __construct() {}

    /**
     * Determine whether the user can browse support plans.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('case.support.plan.read');
    }

    /**
     * Determine whether a specific plan can be viewed.
     * HIERARCHY: Checks user ID against the beneficiary's user account linkage.
     */
    public function view(User $user, CaseSupportPlan $caseSupportPlan): bool
    {
        return $user->can('case.support.plan.read') ||
               $caseSupportPlan->beneficiaryCase->beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether a new support plan can be created.
     */
    public function create(User $user): bool
    {
        return $user->can('case.support.plan.create');
    }

    /**
     * Determine whether the support plan can be updated.
     */
    public function update(User $user, CaseSupportPlan $caseSupportPlan): bool
    {
        return $user->can('case.support.plan.update') ||
               $caseSupportPlan->beneficiaryCase->beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the support plan can be deleted.
     * Requires specific administrative clearance.
     */
    public function delete(User $user, CaseSupportPlan $caseSupportPlan): bool
    {
        return $user->can('case.support.plan.delete');
    }
}
