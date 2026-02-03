<?php

namespace Modules\Entities\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;
use Modules\Entities\Models\DonorReport;

/**
 * Class DonorReportPolicy
 * * Enforces security boundaries for Donor Reports.
 * This policy ensures strict Data Segregation, preventing unauthorized access
 * between different donor entities while allowing global access for administrative roles.
 * * @package Modules\Entities\Policies
 */
class DonorReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view a collection of reports.
     * * Logic:
     * - Admins: Full access to all entity reports.
     * - Users: Can only view reports where their associated entity matches the request context.
     * * @param User $user The authenticated user.
     * @param int $donorEntityId The ID of the donor entity being queried.
     * @return bool
     */
    public function viewAny(User $user, int $donorEntityId): bool
    {
        return $user->hasRole('admin') || $user->entity_id === $donorEntityId;
    }

    /**
     * Determine if the user can view a specific report instance.
     * * @param User $user The authenticated user.
     * @param DonorReport $report The specific report model being accessed.
     * @return bool
     */
    public function view(User $user, DonorReport $report): bool
    {
        return $user->hasRole('admin') || $user->entity_id === $report->donor_entity_id;
    }

    /**
     * Determine if the user can trigger the generation of a new report.
     * * This prevents users from one entity from generating reports for another,
     * which could lead to resource exhaustion or data exposure.
     * * @param User $user
     * @param int $donorEntityId Contextual ID for the new report.
     * @return bool
     */
    public function generate(User $user, int $donorEntityId): bool
    {
        return $user->hasRole('admin') || $user->entity_id === $donorEntityId;
    }

    /**
     * Determine if the user can permanently remove a report snapshot.
     * * Logic: Restricted to 'admin' role only to maintain audit trail integrity.
     * * @param User $user
     * @param DonorReport $report
     * @return bool
     */
    public function delete(User $user, DonorReport $report): bool
    {
        return $user->hasRole('admin');
    }
}
