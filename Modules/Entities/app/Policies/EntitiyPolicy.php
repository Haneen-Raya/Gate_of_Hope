<?php

namespace Modules\Entities\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\User;
use Modules\Entities\Models\Entitiy;

/**
 * Class EntitiyPolicy
 *
 * Authorization policy responsible for controlling access
 * to Entity resources within the system.
 *
 * This policy enforces:
 * - Role-based access control (RBAC)
 * - Permission-based access using spatie/laravel-permission
 * - Ownership-based (self) access restrictions
 * - Context-aware rules (e.g. referrals, funding visibility)
 *
 * Security principles applied:
 * - Least privilege
 * - Separation of duties
 * - Explicit denial by default
 *
 * @package Modules\Entities\Policies
 */
class EntitiyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view a list of entities.
     *
     * Used for index/listing endpoints.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin')
            || $user->hasRole('program_manager')
            || $user->hasRole('case_coordinator');
    }

    /**
     * Determine whether the user can view a specific entity.
     *
     * - Admin can view any entity
     * - Program roles with entities.read permission can view any entity
     * - Entity account users can only view their own entity profile
     *
     * Ownership Rule:
     * - Entity user must match entity.user_id
     *
     * @param  User     $user
     * @param  Entitiy  $entity
     * @return bool
     */
    public function view(User $user, Entitiy $entity): bool
    {
        return $user->hasRole('admin')

            //program roles can view all entities
            || $user->can('entities.read')

            // Entity user can view only his owns entity
            || $user->can('entities.read.self') && $user->id === $entity->user_id;
    }

    /**
     * Determine whether the user can create a new entity.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update an entity.
     *
     * Business Rules:
     *
     * - Admin can update any entity
     * - Entity accounts can update only their own profile
     *
     * Restrictions:
     * - Entity users should not update operational flags such as:
     *   can_receive_referrals, can_fund_programs, etc.
     * 
     * @param  User     $user
     * @param  Entitiy  $entity
     * @return bool
     */
    public function update(User $user, Entitiy $entity): bool
    {
        return $user->hasRole('admin')

            //Full update permission
            || $user->can('entities.update')

            // Entity can update only itself
            || $user->can('entities.update.self') && $user->id === $entity->user_id;
    }

    /**
     * Determine whether the user can soft-delete an entity.
     */
    public function delete(User $user, Entitiy $entity): bool
    {
        return false;
    }

    /**
     * Prevent restoring deleted entities except admin
     */
    public function restore(User $user, Entitiy $entity): bool
    {
        return false;
    }

    /**
     * Force delete (never allowed)
     */
    public function forceDelete(User $user, Entitiy $entity): bool
    {
        return false;
    }
}
