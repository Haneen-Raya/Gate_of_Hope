<?php

namespace Modules\CaseManagement\Policies;

use Modules\Core\Models\User;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class BeneficiaryCasePolicy
 * * Manages authorization logic for BeneficiaryCase models.
 * Ensures that only authorized users (Admins or assigned Case Managers)
 * can perform specific actions on case records.
 * * @package Modules\CaseManagement\Policies
 */
class BeneficiaryCasePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * * Grant absolute access to users with the 'admin' role,
     * bypassing subsequent policy methods.
     * * @param User $user The currently authenticated user.
     * @param string $ability The ability being checked.
     * @return bool|void
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any beneficiary cases.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'case.read' permission.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('case.read');
    }

    /**
     * Determine whether the user can view a specific beneficiary case.
     * * @param User $user The currently authenticated user.
     * @param BeneficiaryCase $case The case instance being accessed.
     * @return bool True if the user has 'case.read' permission.
     */
    public function view(User $user, BeneficiaryCase $case)
    {
        return $user->hasPermissionTo('case.read');
    }

    /**
     * Determine whether the user can create a new beneficiary case.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'case.create' permission.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('case.create');
    }

    /**
     * Determine whether the user can update the beneficiary case.
     * * Strict ownership check: Only the assigned Case Manager
     * can update the case details.
     * * @param User $user The currently authenticated user.
     * @param BeneficiaryCase $case The case instance being updated.
     * @return bool True if user ID matches the case_manager_id.
     */
    public function update(User $user, BeneficiaryCase $case)
    {
        return $user->id === $case->case_manager_id;
    }

    /**
     * Determine whether the user can delete the beneficiary case.
     * * Strict ownership check: Only the assigned Case Manager
     * can delete the case record.
     * * @param User $user The currently authenticated user.
     * @param BeneficiaryCase $case The case instance being deleted.
     * @return bool True if user ID matches the case_manager_id.
     */
    public function delete(User $user, BeneficiaryCase $case)
    {
        return $user->id === $case->case_manager_id;
    }
}
