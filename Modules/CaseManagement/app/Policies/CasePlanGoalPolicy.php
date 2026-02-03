<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CasePlanGoal;
use Modules\Core\Models\User;

/**
 * Class CasePlanGoalPolicy
 *
 * Manages authorization for specific objectives within a Case Support Plan.
 * Implements complex ownership checks across the case hierarchy.
 *
 * @package Modules\CaseManagement\Policies
 */
class CasePlanGoalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view goals in general.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('case.plan.goal.read');
    }

    /**
     * Determine if a user can view a specific goal.
     * * HIERARCHICAL CHECK:
     * Access is granted if the user is the owner of the beneficiary profile
     * linked through: Goal -> Support Plan -> Beneficiary Case -> Beneficiary.
     *
     * @param User $user
     * @param CasePlanGoal $casePlanGoal
     * @return bool
     */
    public function view(User $user, CasePlanGoal $casePlanGoal): bool
    {
        return $user->can('case.plan.goal.read') ||
               $casePlanGoal->caseSupportPlan->beneficiaryCase->beneficiary->user_id == $user->id;
    }

    /**
     * Determine if a user can create goals.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('case.plan.goal.create');
    }

    /**
     * Determine if a user can update a goal.
     * * CASE MANAGER OVERRIDE:
     * Allows the designated Case Manager of the parent case to modify goals
     * even without broad administrative permissions.
     *
     * @param User $user
     * @param CasePlanGoal $casePlanGoal
     * @return bool
     */
    public function update(User $user, CasePlanGoal $casePlanGoal): bool
    {
        return $user->can('case.plan.goal.update') ||
               $casePlanGoal->caseSupportPlan->beneficiaryCase->case_manager_id == $user->id;
    }

    /**
     * Determine if a user can delete a goal.
     *
     * @param User $user
     * @param CasePlanGoal $casePlanGoal
     * @return bool
     */
    public function delete(User $user, CasePlanGoal $casePlanGoal): bool
    {
        return $user->can('case.plan.goal.delete');
    }
}
