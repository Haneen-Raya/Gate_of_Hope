<?php

namespace Modules\Programs\Policies;

use Modules\Core\Models\User;
use Modules\Programs\Models\Program;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ProgramPolicy
 * * Defines the access control layer for the Program model.
 * * This policy implements the security matrix requirements, ensuring that
 * specific actions (CRUD) are only performed by authorized personnel based on
 * roles (Super Admin, Program Manager) and individual permissions.
 * * @package Modules\Programs\Policies
 * @see \Modules\Programs\Models\Program For the associated model.
 */
class ProgramPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * * Handles the "God-mode" access for Super Administrators, allowing them
     * to bypass all subsequent policy checks.
     * * @param User $user The currently authenticated user.
     * @param string $ability The method/action being checked.
     * @return bool|null Returns true to bypass, or null to continue to specific methods.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('Super Administrator') ? true : null;
    }

    /**
     * Determine whether the user can view a paginated list of programs.
     * * Supports multi-permission checks to accommodate Program Managers,
     * Donors, and Researchers who might have different read scopes.
     * * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['programs.read', 'program.read.all']);
    }

    /**
     * Determine whether the user can view the details of a specific program.
     * * @param User $user
     * @param Program $program The program instance being accessed.
     * @return bool
     */
    public function view(User $user, Program $program): bool
    {
        return $user->hasPermissionTo('programs.read');
    }

    /**
     * Determine whether the user can create new programs.
     * * Usually restricted to Program Managers or administrative staff.
     * * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('programs.create');
    }

    /**
     * Determine whether the user can update a specific program.
     * * Implements a "Ownership or Authority" logic:
     * 1. Users with 'programs.update' permission.
     * 2. The original creator of the program ($program->created_by).
     * * @param User $user
     * @param Program $program
     * @return bool
     */
    public function update(User $user, Program $program): bool
    {
        return $user->hasPermissionTo('programs.update') || $user->id === $program->created_by;
    }

    /**
     * Determine whether the user can permanently delete a program.
     * * This is a high-sensitivity action requiring specific 'programs.delete' clearance.
     * * @param User $user
     * @param Program $program
     * @return bool
     */
    public function delete(User $user, Program $program): bool
    {
        return $user->hasPermissionTo('programs.delete');
    }
}
