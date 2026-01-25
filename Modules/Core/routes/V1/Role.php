<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\V1\RoleManagementController;

/*
|--------------------------------------------------------------------------
| Role Management Module - Admin API (Open Access)
|--------------------------------------------------------------------------
| Controller: RoleManagementController
| Functionality: Managing Spatie Roles & User Assignments
| Security Note: Access is not restricted by Sanctum middleware.
|--------------------------------------------------------------------------
*/

Route::prefix('role')->group(function () {

    /**
     * @name 1. List All Roles
     * @path GET /api/admin/roles
     * * @description Fetches all available roles (e.g., Admin, Super Admin, User)
     * defined in the system.
     * @return \Illuminate\Http\JsonResponse
     */
    Route::get('/roles', [RoleManagementController::class, 'index']);

    /**
     * @name 2. Assign Role to User
     * @path POST /api/admin/users/{user}/roles/assign
     * * @url_params:
     * - user (int): The ID of the target user.
     * * @body_payload (UserRoleRequest):
     * - role (string/required): The exact name of the role to be added.
     * * @description Adds a role to the user's existing roles. Does not remove old roles.
     */
    Route::post('/users/{user}/roles/assign', [RoleManagementController::class, 'assign']);

    /**
     * @name 3. Revoke Role from User
     * @path POST /api/admin/users/{user}/roles/revoke
     * * @description Removes a specific role from a user. If the user does not
     * have the role, it returns a conflict/not-found response.
     */
    Route::post('/users/{user}/roles/revoke', [RoleManagementController::class, 'revoke']);

    /**
     * @name 4. Sync/Update User Role
     * @path PUT /api/admin/users/{user}/roles/update
     * * @description Replaces all current user roles with the single role provided.
     * Ideal for strict "One User - One Role" logic.
     */
    Route::put('/users/{user}/roles/update', [RoleManagementController::class, 'update']);
});
