<?php

namespace Modules\CaseManagement\Policies;

use Modules\Core\Models\User;
use Modules\CaseManagement\Models\CaseSession;
use Modules\CaseManagement\Models\BeneficiaryCase;

/**
 * Class CaseSessionPolicy
 *
 * This policy governs the authorization logic for Case Sessions, ensuring high-level
 * data privacy and clinical confidentiality.
 *
 * ACCESS HIERARCHY:
 * 1. Admin: Unconditional administrative access (Audited).
 * 2. Specialist: Owner-based access (Can Create/Update/Delete their own sessions).
 * 3. Case Coordinator: Supervisory access (Read-only for managed cases).
 * 4. Beneficiary: Self-service access (Read-only for own records).
 *
 * @package Modules\CaseManagement\Policies
 */
class CaseSessionPolicy
{
    /**
     * Pre-authorization Gate.
     * * Grants System Administrators full access before any other methods are executed.
     * LOGGING REQUIREMENT: Any action performed via this gate should be recorded
     * in the system's security audit logs.
     *
     * @param User   $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine access to the session index for a specific case.
     * * ACCESS RULES:
     * - Specialists must be the conductors of at least one session in the case.
     * - Case Coordinators must be the officially assigned managers of the case.
     * - Beneficiaries must be the owners of the case.
     *
     * @param User             $user
     * @param BeneficiaryCase  $case
     * @return bool
     */
    public function viewAny(User $user, BeneficiaryCase $case): bool
    {
        if ($user->hasRole('specialist')) {
            return CaseSession::where('beneficiary_case_id', $case->id)
                ->where('conducted_by', $user->id)
                ->exists();
        }

        if ($user->hasRole('case_coordinator') && $case->case_manager_id === $user->id) {
            return true;
        }

        if ($user->hasRole('beneficiary') && $case->beneficiary_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine access to a specific session's details.
     * * Implements "Owner-only" modification logic while allowing "Related-party" viewing.
     *
     * @param User         $user
     * @param CaseSession  $session
     * @return bool
     */
    public function view(User $user, CaseSession $session): bool
    {
        $case = $session->beneficiaryCase;

        if ($user->hasRole('specialist') && $session->conducted_by === $user->id) {
            return true;
        }

        if ($user->hasRole('case_coordinator') && $case->case_manager_id === $user->id) {
            return true;
        }

        if ($user->hasRole('beneficiary') && $case->beneficiary_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create a session.
     * * Current Logic: Limits creation to specialists who already have at least
     * one session record in the targeted case.
     *
     * @param User             $user
     * @param BeneficiaryCase  $case
     * @return bool
     */
    public function create(User $user, BeneficiaryCase $case): bool
    {
        return $user->hasRole('specialist') &&
               CaseSession::where('beneficiary_case_id', $case->id)
                          ->where('conducted_by', $user->id)
                          ->exists();
    }

    /**
     * Determine if a session can be updated.
     * * STRICT RULE: Only the primary conductor (specialist) who logged the
     * session can edit its content.
     *
     * @param User         $user
     * @param CaseSession  $session
     * @return bool
     */
    public function update(User $user, CaseSession $session): bool
    {
        return $user->hasRole('specialist') && $session->conducted_by === $user->id;
    }

    /**
     * Determine if a session can be deleted.
     * * It is recommended to use Soft Deletes in conjunction with this method
     * to maintain clinical audit trails.
     *
     * @param User         $user
     * @param CaseSession  $session
     * @return bool
     */
    public function delete(User $user, CaseSession $session): bool
    {
        return $user->hasRole('specialist') && $session->conducted_by === $user->id;
    }

    /**
     * Filter-based authorization.
     * * Checks if the user has the right to view sessions conducted by a specific specialist,
     * usually for reporting or supervisory purposes.
     *
     * @param User $user
     * @param int  $specialistId
     * @return bool
     */
    public function viewBySpecialist(User $user, int $specialistId): bool
    {
        if ($user->hasRole('specialist') && $user->id === $specialistId) {
            return true;
        }

        if ($user->hasRole('case_coordinator')) {
            return BeneficiaryCase::where('case_coordinator_id', $user->id)
                ->whereHas('sessions', function ($q) use ($specialistId) {
                    $q->where('conducted_by', $specialistId);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Authorization for metadata/metrics.
     * * Reuses viewAny logic to determine if the user can see session statistics.
     *
     * @param User             $user
     * @param BeneficiaryCase $case
     * @return bool
     */
    public function count(User $user, BeneficiaryCase $case): bool
    {
        return $this->viewAny($user, $case);
    }
}
