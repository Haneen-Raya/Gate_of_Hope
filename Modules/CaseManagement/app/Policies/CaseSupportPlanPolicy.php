<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CaseSupportPlan;
use Modules\Core\Models\User;

class CaseSupportPlanPolicy
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
        return $user->can('case.support.plan.read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseSupportPlan $caseSupportPlan): bool
    {
        return $user->can('case.support.plan.read') || $caseSupportPlan->beneficiaryCase->beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('case.support.plan.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseSupportPlan $caseSupportPlan): bool
    {
        return $user->can('case.support.plan.update') || $caseSupportPlan->beneficiaryCase->beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseSupportPlan $caseSupportPlan): bool
    {
        return $user->can('case.support.plan.delete');
    }
}
