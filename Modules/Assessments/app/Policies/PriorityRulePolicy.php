<?php

namespace Modules\Assessments\Policies;

use Modules\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class PriorityRulePolicy
 * * Governs the automated scoring and priority assignment logic.
 * Includes a global override for administrators and supports cross-permission checks.
 * * @package Modules\Assessments\Policies
 */
class PriorityRulePolicy
{
    use HandlesAuthorization;

    /**
     * Global Administrative Override.
     * * Grants full access to all priority rule abilities if the user possesses the 'admin' role,
     * executing before any other policy method.
     * * @param User $user The currently authenticated user.
     * @param string $ability The specific capability being validated.
     * @return bool|void
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the list of priority rules.
     * * Access is granted if the user has either priority rule or general assessment read permissions.
     * * @param User $user The currently authenticated user.
     * @return bool True if authorized by any specified permissions.
     */
    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['priority_rules.read', 'assessments.read']);
    }

    /**
     * Determine whether the user can define new priority assignment rules.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'priority_rules.create' permission.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('priority_rules.create');
    }

    /**
     * Determine whether the user can update existing priority rules.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'priority_rules.update' permission.
     */
    public function update(User $user)
    {
        return $user->hasPermissionTo('priority_rules.update');
    }

    /**
     * Determine whether the user can remove a priority rule from the system.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'priority_rules.delete' permission.
     */
    public function delete(User $user)
    {
        return $user->hasPermissionTo('priority_rules.delete');
    }
}
