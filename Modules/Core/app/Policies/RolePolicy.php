<?php

namespace Modules\Core\Policies;

use Modules\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}
    public function manageRoles(User $user)
    {
        return $user->hasRole('admin');
    }
}
