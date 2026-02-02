<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CaseEvent;
use Modules\Core\Models\User;

class CaseEventPolicy
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
        return $user->can('case.event.read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseEvent $caseEvent): bool
    {
        return $user->can('case.event.read') || $caseEvent->beneficiary_id == $user->beneficiary->id;
    }
}
