<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class RoleManagementService
 * * This service handles the business logic for managing user roles using the Spatie Permission package.
 * It provides methods to fetch roles, sync, assign, and revoke them from users.
 */
class RoleManagementService
{
    /**
     * Get a list of all available roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Update and synchronize a user's role.
     *
     * @param User $user The user instance to update.
     * @param string $newRoleName The name of the role to assign.
     * @return User The user instance with updated role relations.
     */
    public function updateUserRole(User $user, string $newRoleName): User
    {
        // syncRoles is the perfect method for this.
        // It accepts a role name, an ID, a Role model, or an array of them.
        $user->syncRoles([$newRoleName]);

        Log::info("User {$user->id}'s role synchronized to '{$newRoleName}'");

        // Return the user with their roles reloaded to confirm the change.
        return $user->load('roles');
    }

    /**
     * Assign a specific role to a user.
     *
     * @param User $user The user instance.
     * @param string $roleName The name of the role to be assigned.
     * @return User The user instance with updated role relations.
     */
    public function assignRoleToUser(User $user, string $roleName): User
    {
        // The assignRole method is smart enough not to add the role if it already exists.
        $user->assignRole($roleName);
        Log::info("Role '{$roleName}' assigned to user {$user->id}");

        // Return the user with their roles reloaded.
        return $user->load('roles');
    }

    /**
     * Revoke a specific role from a user.
     *
     * @param User $user The user instance.
     * @param string $roleName The name of the role to be removed.
     * @return User The user instance with updated role relations.
     */
    public function revokeRoleFromUser(User $user, string $roleName): User
    {
        // Check if the user actually has this role before trying to remove it.
        if ($user->hasRole($roleName)) {
            $user->removeRole($roleName);
            Log::info("Role '{$roleName}' revoked from user {$user->id}");
        }

        // Return the user with their roles reloaded.
        return $user->load('roles');
    }
}
