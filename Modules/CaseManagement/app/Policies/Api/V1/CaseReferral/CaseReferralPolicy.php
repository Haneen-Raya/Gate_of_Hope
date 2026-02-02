<?php

namespace Modules\CaseManagement\Policies\Api\V1\CaseReferral;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Enums\CaseReferralStatus;
use Modules\CaseManagement\Models\CaseReferral;
use Modules\Core\Models\User;

class CaseReferralPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseReferral $caseReferral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseReferral $caseReferral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseReferral $caseReferral):bool
    {
        return false;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseReferral $caseReferral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseReferral $caseReferral): bool
    {
        return false;
    }

    /**
     *
     */
    public function updateStatus(User $user, CaseReferral $referral, CaseReferralStatus $to): bool
    {
        return match ($to) {
            CaseReferralStatus::ACCEPTED,
            CaseReferralStatus::REJECTED,
            CaseReferralStatus::COMPLETED
                => (($user->entitiy->user_id === $referral->receiver_entity_id) && $user->hasRole('community_provider')),

            CaseReferralStatus::CANCELLED
                => $user->hasRole(['case_coordinator', 'program_manager']),

            default => false,
    };
}
}
