<?php

namespace Modules\Core\Http\Controllers\Api\V1;

use Illuminate\Http\Response;
use Modules\Core\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Core\Services\RoleManagementService;
use Modules\Core\Http\Requests\V1\Role\UserRoleRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class RoleManagementController
 * * Manages the administrative tasks related to user roles, including
 * listing, assigning, updating, and revoking roles using Spatie Laravel-Permission.
 * * @package Modules\Core\Http\Controllers\Api\V1
 */
class RoleManagementController extends Controller
{
    use AuthorizesRequests;

    /**
     * @var RoleManagementService The service responsible for role business logic.
     */
    protected RoleManagementService $roleService;

    /**
     * RoleManagementController constructor.
     * * @param RoleManagementService $roleService Dependency injection of the role service.
     */
    public function __construct(RoleManagementService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * List all available roles in the system.
     * * Validates if the authenticated user has 'manageRoles' authority.
     * * @return JsonResponse List of all system roles.
     */
    public function index(): JsonResponse
    {
        $this->authorize('manageRoles', User::class);
        $roles = $this->roleService->getAllRoles();
        return $this->successResponse('Roles retrieved successfully.', $roles);
    }

    /**
     * Assign a specific role to a user.
     * * @param UserRoleRequest $request Validated request (Only Admins allowed).
     * @param User $user The target user receiving the role via Route Model Binding.
     * @return JsonResponse Success or conflict response if the role exists.
     */
    public function assign(UserRoleRequest $request, User $user): JsonResponse
    {
        //$this->authorize('manageRoles', User::class);
        $validatedData = $request->validated();
        $roleName = $validatedData['role'];

        $updatedUser = $this->roleService->assignRoleToUser($user, $roleName);

        if (!$updatedUser) {
            return $this->errorResponse("User already has the '{$roleName}' role.", null, Response::HTTP_CONFLICT);
        }

        return $this->successResponse(
            "Role '{$roleName}' assigned to user successfully.",
            $updatedUser
        );
    }

    /**
     * Replace all current user roles with a single new role.
     * * Useful for strict single-role enforcement.
     * * @param UserRoleRequest $request Validated request.
     * @param User $user The target user.
     * @return JsonResponse Updated user data.
     */
    public function update(UserRoleRequest $request, User $user): JsonResponse
    {
        $this->authorize('manageRoles', User::class);
        $validatedData = $request->validated();
        $roleName = $validatedData['role'];

        $updatedUser = $this->roleService->updateUserRole($user, $roleName);

        return $this->successResponse(
            "User's role has been updated to '{$roleName}'.",
            $updatedUser
        );
    }

    /**
     * Remove a specific role from a user.
     * * @param UserRoleRequest $request Validated request.
     * @param User $user The target user.
     * @return JsonResponse Success or 404 if user does not possess the role.
     */
    public function revoke(UserRoleRequest $request, User $user): JsonResponse
    {
        $this->authorize('manageRoles', User::class);
        $validatedData = $request->validated();
        $roleName = $validatedData['role'];

        $updatedUser = $this->roleService->revokeRoleFromUser($user, $roleName);

        if (!$updatedUser) {
            return $this->errorResponse("User does not have the '{$roleName}' role to revoke.", null, Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse(
            "Role '{$roleName}' revoked from user successfully.",
            $updatedUser
        );
    }
}
