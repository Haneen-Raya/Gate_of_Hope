<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CasePlanGoal;
use Modules\Core\Models\User;

class CasePlanGoalPolicy
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
        return $user->can('case.plan.goal.read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CasePlanGoal $casePlanGoal): bool
    {
        return $user->can('case.plan.goal.read') || $casePlanGoal->caseSupportPlan->beneficiaryCase->beneficiary->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('case.plan.goal.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CasePlanGoal $casePlanGoal): bool
    {
        return $user->can('case.plan.goal.update') || $casePlanGoal->caseSupportPlan->beneficiaryCase->case_manager_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CasePlanGoal $casePlanGoal): bool
    {
        return $user->can('case.plan.goal.delete');
    }
}
