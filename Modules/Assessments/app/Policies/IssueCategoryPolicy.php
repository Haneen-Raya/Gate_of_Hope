<?php

namespace Modules\Assessments\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;

/**
 * Class IssueCategoryPolicy
 *
 * Governs authorization logic for the IssueCategory model.
 * Acts as a security gatekeeper for high-level classification data, ensuring only
 * authorized administrative staff can modify the structural hierarchy.
 *
 * @package Modules\Assessments\Policies
 */
class IssueCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Determine whether the user can view any issue categories.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->can('issue_categories.read');
    }

    /**
     * Determine whether the user can create an issue category.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('issue_categories.create');
    }

    /**
     * Determine whether the user can update an issue category.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('issue_categories.update');
    }

    /**
     * Determine whether the user can delete an issue category.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('issue_categories.delete');
    }

    /**
     * Determine whether the user can archive an issue category.
     *
     * @param User $user The authenticated user.
     * @return bool
     */
    public function archieve(User $user)
    {
        return $user->can('issue_categories.archive');
    }
}
