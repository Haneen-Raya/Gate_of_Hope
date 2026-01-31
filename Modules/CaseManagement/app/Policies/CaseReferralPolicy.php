<?php

namespace Modules\CaseManagement\Policies\CaseReferral;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Enums\V1\CaseReferralStatus;
use Modules\CaseManagement\Models\CaseReferral;
use Modules\Core\Models\User;
use Illuminate\Auth\Access\Response;

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
     * Determine whether the user can view the given case caseReferral.
     *
     * Access is granted if:
     * - User is an admin
     * - User is the beneficiary of the case
     * - User is the case manager of the related beneficiary case
     * - User belongs to the receiving entity
     *
     * @param User $user
     * @param CaseReferral $caseReferral
     *
     * @return bool
     */
    public function view(User $user, CaseReferral $caseReferral): bool
    {
        return
            // 1. Admin has full access
            $user->hasRole('admin')

            // 2. beneficiary of the case
            || ($user->hasRole('beneficiary') && $caseReferral->isForBeneficiary($user))

            // 3. Case coordinator (case manager) owns the case
            || $caseReferral->isManagedBy($user)

            // 4. Receiving entity can view caseReferrals assigned to it
            || $caseReferral->isAssignedToEntity($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,$beneficiaryCase): bool
    {
        $caseManagerId = is_int($beneficiaryCase)
            ? \Modules\CaseManagement\Models\BeneficiaryCase::find($beneficiaryCase)?->case_manager_id
            : $beneficiaryCase->case_manager_id;

        return $user->id === $caseManagerId;
    }

    /**
     * Determine whether the user can update a case referral.
     *
     * Access is granted if the user is:
     * - Admin
     * - Case manager of the beneficiary case
     *
     * @param User $user
     * @param CaseReferral $caseReferral
     *
     * @return bool
     */
    public function update(User $user, CaseReferral $caseReferral): bool
    {
        return  $user->hasRole('admin')
                || $caseReferral->isManagedBy($user) ;
    }

    /**
     * Determine whether the user can delete a case referral.
     *
     * Access is granted if the user is:
     * - Admin
     * - Case manager of the beneficiary case
     *
     * @param User $user
     * @param CaseReferral $caseReferral
     *
     * @return bool
     */
    public function delete(User $user, CaseReferral $caseReferral): bool
    {
        return  $user->hasRole('admin')
                || $caseReferral->isManagedBy($user) ;

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
