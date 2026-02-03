<?php

namespace Modules\Entities\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Core\Models\User;
use Modules\Entities\Models\DonorReport;

class DonorReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any reports for a specific donor entity.
     *
     * @param User $user
     * @param int $donorEntityId
     * @return bool
     */
    public function viewAny(User $user, int $donorEntityId): bool
    {
        return $user->hasRole('admin') || $user->entity_id === $donorEntityId;
    }

    /**
     * Determine if the user can view a specific report.
     *
     * @param User $user
     * @param DonorReport $report
     * @return bool
     */
    public function view(User $user, DonorReport $report): bool
    {
        return $user->hasRole('admin') || $user->entity_id === $report->donor_entity_id;
    }

    /**
     * Determine if the user can generate a report for a specific donor entity.
     *
     * @param User $user
     * @param int $donorEntityId
     * @return bool
     */
    public function generate(User $user, int $donorEntityId): bool
    {
        return $user->hasRole('admin') || $user->entity_id === $donorEntityId;
    }

    /**
     * Determine if the user can delete a report (usually admin only)
     *
     * @param User $user
     * @param DonorReport $report
     * @return bool
     */
    public function delete(User $user, DonorReport $report): bool
    {
        return $user->hasRole('admin');
    }
}   