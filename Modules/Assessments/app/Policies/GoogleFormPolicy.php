<?php

namespace Modules\Assessments\Policies;

use Modules\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class GoogleFormPolicy
 * * Handles authorization logic for Google Forms integration.
 * Links specific administrative actions to their corresponding system permissions.
 * * @package Modules\Assessments\Policies
 */
class GoogleFormPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * Determine whether the user can view the list of Google Forms.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'google_forms.read' permission.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('google_forms.read');
    }

    /**
     * Determine whether the user can create a new Google Form entry.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'google_forms.create' permission.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('google_forms.create');
    }

    /**
     * Determine whether the user can update Google Form configurations.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'google_forms.update' permission.
     */
    public function update(User $user)
    {
        return $user->hasPermissionTo('google_forms.update');
    }

    /**
     * Determine whether the user can delete a Google Form configuration.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'google_forms.delete' permission.
     */
    public function delete(User $user)
    {
        return $user->hasPermissionTo('google_forms.delete');
    }

    /**
     * Determine whether the user can trigger the Google Form data import process.
     * * @param User $user The currently authenticated user.
     * @return bool True if the user has 'google_forms.import' permission.
     */
    public function import(User $user)
    {
        return $user->hasPermissionTo('google_forms.import');
    }
}
