<?php

namespace Modules\Programs\Policies;

use Modules\Core\Models\User;
use Modules\Programs\Models\Program;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ProgramPolicy
 * * Handles authorization logic for Program model operations based on
 * the project's security matrix (Section 4).
 *
 * @package Modules\Programs\Policies
 */
class ProgramPolicy
{
    use HandlesAuthorization;

    /**
     * Global bypass for Super Administrator.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('Super Administrator') ? true : null;
    }

    /**
     * Determine if the user can view the list of programs.
     * Allowed for: Program Managers, Donors, and Researchers (Aggregated).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['programs.read', 'program.read.all']);
    }

    /**
     * Determine if the user can view a specific program.
     */
    public function view(User $user, Program $program): bool
    {
        return $user->hasPermissionTo('programs.read');
    }

    /**
     * Determine if the user can create a program.
     * Restricted to Program Managers.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('programs.create');
    }

    /**
     * Determine if the user can update the program.
     * Allowed if user has permission AND (is manager OR the creator).
     */
    public function update(User $user, Program $program): bool
    {
        return $user->hasPermissionTo('programs.update') || $user->id === $program->created_by;
    }

    /**
     * Determine if the user can delete the program.
     * High-level permission required.
     */
    public function delete(User $user, Program $program): bool
    {
        return $user->hasPermissionTo('programs.delete');
    }
}
