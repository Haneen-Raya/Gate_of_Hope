<?php

namespace Modules\Programs\Policies;

use Modules\Core\Models\User;
use Modules\Programs\Models\ProgramResource;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ProgramResourcePolicy
 * * Manages authorization logic for ProgramResource model operations.
 * It ensures that only users with specific permissions or roles can
 * access or modify resource-related data.
 * * @package Modules\Programs\Policies
 */
class ProgramResourcePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any program resources.
     * * Required Permissions: 'resources.read' OR 'programs.read'
     * * @param User $user The authenticated user instance
     * @return bool True if authorized, false otherwise
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['resources.read', 'programs.read']);
    }

    /**
     * Determine whether the user can view a specific program resource.
     * * Required Permission: 'resources.read'
     * * @param User $user The authenticated user instance
     * @param ProgramResource $resource The resource model instance to view
     * @return bool True if authorized, false otherwise
     */
    public function view(User $user, ProgramResource $resource): bool
    {
        return $user->hasPermissionTo('resources.read');
    }

    /**
     * Determine whether the user can create/allocate new resources.
     * * Required Permission: 'resources.allocate'
     * * @param User $user The authenticated user instance
     * @return bool True if authorized, false otherwise
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('resources.allocate');
    }

    /**
     * Determine whether the user can update an existing program resource.
     * * Required Permission: 'resources.update'
     * * @param User $user The authenticated user instance
     * @param ProgramResource $resource The resource model instance to update
     * @return bool True if authorized, false otherwise
     */
    public function update(User $user, ProgramResource $resource): bool
    {
        return $user->hasPermissionTo('resources.update');
    }

    /**
     * Determine whether the user can delete a program resource.
     * * Allowed for: Users with 'admin' role OR 'programs.delete' permission.
     * * @param User $user The authenticated user instance
     * @param ProgramResource $resource The resource model instance to delete
     * @return bool True if authorized, false otherwise
     */
    public function delete(User $user, ProgramResource $resource): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('programs.delete');
    }
}
