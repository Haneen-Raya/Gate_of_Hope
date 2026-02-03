<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CaseEvent;
use Modules\Core\Models\User;

/**
 * Class CaseEventPolicy
 *
 * Governs access to the chronological timeline of case events.
 * Handles both staff-level administrative access and beneficiary-level self-service access.
 *
 * @package Modules\CaseManagement\Policies
 */
class CaseEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the entire case timeline.
     * Required permission: 'case.event.read'
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('case.event.read');
    }

    /**
     * Determine whether the user can view a specific event record.
     * * AUTH LOGIC:
     * - Grants access if user has global read permission.
     * - Grants access if the user is the beneficiary linked directly to this event.
     *
     * @param User $user
     * @param CaseEvent $caseEvent
     * @return bool
     */
    public function view(User $user, CaseEvent $caseEvent): bool
    {
        return $user->can('case.event.read') || $caseEvent->beneficiary_id == $user->beneficiary->id;
    }
}
