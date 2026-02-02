<?php

namespace Modules\Programs\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;
use Modules\Programs\Models\ActivityAttendance;

class ActivityAttendancePolicy
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
     * Determine whether the user can view an attendance record.
     *
     * Allowed if the user is:
     * - Admin
     * - The beneficiary owner of the record
     * - The trainer who recorded it
     * - The program manager of the related program
     * - The provider entity responsible for the activity
     */
    public function view(User $user, ActivityAttendance $attendance): bool
    {
        return
            // Admin full access
            $user->hasRole('admin')

            // Beneficiary can view only own attendance
            || ($user->hasRole('beneficiary') && $attendance->isForBeneficiary($user))

            // Trainer who recorded it
            || ($user->hasRole('trainer') && $attendance->isRecordedBy($user))

            // Program manager can view attendance in their programs
            || ($user->hasRole('program_manager') && $attendance->isWithinManagedProgram($user))

            // Provider entity can view attendance in their activities
            || ($user->hasRole('community_provider') && $attendance->belongsToProviderEntity($user));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,$beneficiaryCase): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update an attendance record.
     *
     * Trainers can update only records they recorded.
     */
    public function update(User $user, ActivityAttendance $attendance): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('trainer')
                && $attendance->recordedBy($user)
                && $user->can('activity.attendance.update'));
    }

    /**
     * Determine whether the user can delete an activity.
     */
    public function delete(User $user, ActivityAttendance $attendance): bool
    {
        return  false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActivityAttendance $attendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ActivityAttendance $attendance): bool
    {
        return false;
    }

}

