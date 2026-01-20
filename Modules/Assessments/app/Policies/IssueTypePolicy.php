<?php

namespace Modules\Assessments\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;

class IssueTypePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function viewAny(User $user)
    {
        return $user->can('issue_types.read');
    }

    public function create(User $user)
    {
        return $user->can('issue_types.create');
    }

    public function update(User $user)
    {
        return $user->can('issue_types.update');
    }

    public function delete(User $user)
    {
        return $user->can('issue_types.delete');
    }
        public function archeive(User $user)
    {
        return $user->can('issue_types.archive');
    }
}

