<?php

namespace Modules\CaseManagement\Policies;

use Modules\Core\Models\User;
use Modules\CaseManagement\Models\CaseSession;
use Modules\CaseManagement\Models\BeneficiaryCase;

/**
 * Class CaseSessionPolicy
 *
 * Policy class responsible for controlling access to Case Sessions.
 *
 * Security principles applied:
 * - Least Privilege: Users only get access to what they need.
 * - Role + Ownership + Context-based access: Checks role and ownership of session/case.
 * - Case Sessions are highly sensitive; access should be carefully controlled and audited.
 *
 * Allowed roles:
 * - Specialist (Psychologist / Social Worker)
 * - Case Manager (Read-only)
 * - Beneficiary (Read-only)
 * - System Admin (Exceptional access with audit logging)
 *
 * Methods overview:
 * - before(): Grants System Admin unconditional access before other checks.
 * - viewAny(): Can view all sessions of a specific case.
 * - view(): Can view a specific session.
 * - create(): Can create a session (specialist only, assigned case).
 * - update(): Can update a session (owner specialist only).
 * - delete(): Can delete a session (owner specialist only; soft deletes recommended).
 * - viewBySpecialist(): View sessions filtered by a specific specialist.
 * - count(): Check if user can see session count for a case.
 */
class CaseSessionPolicy
{
    /**
     * Handle authorization before all other checks.
     *
     * System Admin has exceptional access for technical or auditing purposes.
     * ⚠️ Any access granted here MUST be logged in Audit Logs.
     *
     * @param User   $user    The user requesting access
     * @param string $ability The action being attempted
     * @return bool|null       True if access granted, null to continue normal checks
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            // TODO: Trigger Audit Log for administrative access
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view a list of sessions for a beneficiary case.
     *
     * Rules:
     * - Specialist: only for cases assigned to them
     * - Case Manager: only for cases they manage (read-only)
     * - Beneficiary: only their own case sessions (read-only)
     *
     * @param User             $user
     * @param BeneficiaryCase  $case
     * @return bool
     */
    public function viewAny(User $user, BeneficiaryCase $case): bool
    {
        // Assigned specialist
        if ($user->hasRole('specialist')) {
            return CaseSession::where('beneficiary_case_id', $case->id)
                ->where('conducted_by', $user->id)
                ->exists();
        }

        // Assigned case manager (read-only)
        if ($user->hasRole('case_coordinator') && $case->case_manager_id === $user->id) {
            return true;
        }

        // Beneficiary accessing own sessions
        if ($user->hasRole('beneficiary') && $case->beneficiary_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view a specific case session.
     *
     * Rules:
     * - Specialist who created the session
     * - Case Manager managing the related case (read-only)
     * - Beneficiary owning the related case (read-only)
     *
     * @param User         $user
     * @param CaseSession  $session
     * @return bool
     */
    public function view(User $user, CaseSession $session): bool
    {
        $case = $session->beneficiaryCase;

        // Session owner (specialist)
        if ($user->hasRole('specialist') && $session->conducted_by === $user->id) {
            return true;
        }

        // Case manager (read-only access)
        if ($user->hasRole('case_coordinator') && $case->case_manager_id === $user->id) {
            return true;
        }

        // Beneficiary (read-only access)
        if ($user->hasRole('beneficiary') && $case->beneficiary_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create a new session for a case.
     *
     * Rule:
     * - Only the specialist assigned to the case can create sessions.
     *
     * ⚠️ Note: Current implementation checks if a session already exists.
     *        Might need revision: specialists should be allowed to create new sessions
     *        even if no sessions exist yet.
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
     * Determine whether the user can update a case session.
     *
     * Rule:
     * - Only the specialist who created the session can modify it.
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
     * Determine whether the user can delete a case session.
     *
     * Rule:
     * - Only the specialist who owns the session may delete it.
     * - Prefer Soft Deletes and mandatory Audit Logging.
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
     * Determine whether the user can view sessions by a specific specialist.
     *
     * Rules:
     * - Specialist can view their own sessions
     * - Case Manager may view sessions related to cases they manage
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
     * Determine whether the user can view the number of sessions for a case.
     *
     * Note:
     * - Reuses the same rules as viewAny()
     * - Returned data is non-sensitive (count only)
     *
     * @param User            $user
     * @param BeneficiaryCase $case
     * @return bool
     */
    public function count(User $user, BeneficiaryCase $case): bool
    {
        return $this->viewAny($user, $case);
    }
}
