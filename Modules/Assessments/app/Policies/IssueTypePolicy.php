<?php

namespace Modules\Assessments\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;

/**
 * Class IssueTypePolicy
 *
 * Governs authorization logic for the IssueType model.
 * Maps controller actions to specific user permissions to ensure granular access control
 * within the Assessments module.
 *
 * @package Modules\Assessments\Policies
 */
class IssueTypePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Determine whether the user can view a list of issue types.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->can('issue_types.read');
    }

    /**
     * Determine whether the user can create a new issue type.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('issue_types.create');
    }

    /**
     * Determine whether the user can update an existing issue type.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('issue_types.update');
    }

    /**
     * Determine whether the user can delete an issue type.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('issue_types.delete');
    }

    /**
     * Determine whether the user can move an issue type to the archive.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function archeive(User $user)
    {
        return $user->can('issue_types.archive');
    }
}
