<?php

namespace Modules\HumanResources\Policies;

use Modules\Core\Models\User;
use Modules\HumanResources\Models\Specialist;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SpecialistPolicy
 * * Orchestrates granular access control for the Specialist resource.
 * This policy acts as a bridge between the Spatie Permission system and
 * the application's business logic, ensuring that only authorized staff
 * can interact with specialist professional data.
 * * @package Modules\HumanResources\Policies
 */
class SpecialistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can browse the list of specialists.
     * * @param User $user
     * @return bool Granted if user has 'view_specialist' permission.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_specialist');
    }

    /**
     * Determine if the user can view a specific specialist's detailed profile.
     * * @param User $user
     * @param Specialist $specialist
     * @return bool
     */
    public function view(User $user, Specialist $specialist): bool
    {
        return $user->can('view_specialist');
    }

    /**
     * Determine if the user has the authority to onboard a new specialist.
     * * @param User $user
     * @return bool Granted if user has 'create_specialist' permission.
     */
    public function create(User $user): bool
    {
        return $user->can('create_specialist');
    }

    /**
     * Determine if the user can modify an existing specialist's record.
     * * @param User $user
     * @param Specialist $specialist
     * @return bool Granted if user has 'update_specialist' permission.
     */
    public function update(User $user, Specialist $specialist): bool
    {
        return $user->can('update_specialist');
    }

    /**
     * Determine if the user can remove a specialist from the system.
     * * @note This is a sensitive action usually reserved for HR Managers or Admins.
     * @param User $user
     * @param Specialist $specialist
     * @return bool Granted if user has 'delete_specialist' permission.
     */
    public function delete(User $user, Specialist $specialist): bool
    {
        return $user->can('delete_specialist');
    }
}
