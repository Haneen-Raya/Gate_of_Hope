<?php

namespace Modules\Core\Policies;

use Modules\Core\Models\User;
use Modules\Core\Models\Region;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RegionPolicy
 * * Manages authorization logic for geographical regions.
 * Ensures that administrative actions on regions are restricted
 * to users with the appropriate core permissions.
 * * @package Modules\Core\Policies
 */
class RegionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * Determine whether the user can create a new region.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'regions.create' permission.
     */
    public function create(User $user)
    {
        return $user->can('regions.create');
    }

    /**
     * Determine whether the user can update an existing region.
     * * @param User $user The currently authenticated user.
     * @param Region|null $region The specific region instance (optional for validation).
     * @return bool True if the user has 'regions.update' permission.
     */
    public function update(User $user, ?Region $region = null)
    {
        return $user->can('regions.update');
    }

    /**
     * Determine whether the user can delete a region.
     * * @param User $user The currently authenticated user.
     * @param Region|null $region The specific region instance (optional for validation).
     * @return bool True if the user has 'regions.delete' permission.
     */
    public function delete(User $user, ?Region $region = null)
    {
        return $user->can('regions.delete');
    }

    /**
     * Determine whether the user can view the list of all regions.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'regions.read' permission.
     */
    public function viewAny(User $user)
    {
        return $user->can('regions.read');
    }

    /**
     * Determine whether the user can view details of a specific region.
     * * @param User $user The currently authenticated user.
     * @param Region|null $region The specific region instance (optional for validation).
     * @return bool True if the user has 'regions.read' permission.
     */
    public function view(User $user, ?Region $region = null)
    {
        return $user->can('regions.read');
    }
}
