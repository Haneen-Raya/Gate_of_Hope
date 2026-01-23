<?php

namespace Modules\Assessments\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;

class IssueCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function viewAny(User $user)
    {
        return $user->can('issue_categories.read');
    }

    public function create(User $user)
    {
        return $user->can('issue_categories.create');
    }

    public function update(User $user)
    {
        return $user->can('issue_categories.update');
    }

    public function delete(User $user)
    {
        return $user->can('issue_categories.delete');
    }
    public function archieve(User $user)
    {
        return $user->can('issue_categories.archive');
    }
}

