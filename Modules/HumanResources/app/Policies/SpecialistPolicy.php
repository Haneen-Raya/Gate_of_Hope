<?php

namespace Modules\HumanResources\Policies;

use Modules\Core\Models\User;
use Modules\HumanResources\Models\Specialist;

/**
 * Class SpecialistPolicy
 *
 * Handles authorization for actions on Specialists.
 *
 * Permissions:
 * - view_specialist
 * - create_specialist
 * - update_specialist
 * - delete_specialist
 */
class SpecialistPolicy
{
    /**
     * Determine if the user can view all specialists.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_specialist');
    }

    /**
     * Determine if the user can view a specific specialist.
     *
     * @param User $user
     * @param Specialist $specialist
     * @return bool
     */
    public function view(User $user, Specialist $specialist): bool
    {
        return $user->can('view_specialist');
    }

    /**
     * Determine if the user can create a specialist.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_specialist');
    }

    /**
     * Determine if the user can update a specialist.
     *
     * @param User $user
     * @param Specialist $specialist
     * @return bool
     */
    public function update(User $user, Specialist $specialist): bool
    {
        return $user->can('update_specialist');
    }

    /**
     * Determine if the user can delete a specialist.
     *
     * @param User $user
     * @param Specialist $specialist
     * @return bool
     */
    public function delete(User $user, Specialist $specialist): bool
    {
        return $user->can('delete_specialist');
    }
}
