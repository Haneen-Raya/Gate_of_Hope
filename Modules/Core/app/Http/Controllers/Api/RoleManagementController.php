<?php

namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Core\Services\RoleManagementService;
use Modules\Core\Http\Requests\Role\UserRoleRequest;

/**
 * Class RoleManagementController
 * * This controller manages API endpoints for user role administration.
 * It utilizes RoleManagementService to handle the business logic of roles.
 */
class RoleManagementController extends Controller
{
    /**
     * @var RoleManagementService The service instance for role operations.
     */
    protected RoleManagementService $roleService;

    /**
     * RoleManagementController constructor.
     * * @param RoleManagementService $roleService
     */
    public function __construct(RoleManagementService $roleService)
    {
        $this->roleService = $roleService;
    }

    //  /**
    //      * Define the middleware for this controller.
    //      *
    //      * @return array
    //      */
        // public static function middleware(): array
        // {
        //     return [
        //         new Middleware('permission:manage User Role', only: ['index', 'assign', 'update','revoke']),
        //     ];
        // }

    /**
     * List all available roles in the system.
     * * @return JsonResponse Returns a collection of all roles.
     */
    public function index(): JsonResponse
    {
        $roles = $this->roleService->getAllRoles();
        return $this->successResponse('Roles retrieved successfully.', $roles);
    }

    /**
     * Assign a specific role to a user.
     * * @param UserRoleRequest $request Validated request containing the 'role' name.
     * @param User $user The user model instance injected via Route Model Binding.
     * @return JsonResponse Success response with updated user data, or error if conflict occurs.
     */
    public function assign(UserRoleRequest $request, User $user): JsonResponse
    {
        $validatedData = $request->validated();
        $roleName = $validatedData['role'];

        $updatedUser = $this->roleService->assignRoleToUser($user, $roleName);

        if (!$updatedUser) {
            return $this->errorResponse("User already has the '{$roleName}' role.", null, Response::HTTP_CONFLICT); // 409
        }

        return $this->successResponse(
            "Role '{$roleName}' assigned to user successfully.",
            $updatedUser
        );
    }

    /**
     * Replace existing user roles with a new role.
     * * @param UserRoleRequest $request Validated request containing the 'role' name.
     * @param User $user The user model instance.
     * @return JsonResponse Success response with updated user and their new role.
     */
    public function update(UserRoleRequest $request, User $user): JsonResponse
    {
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
     * * @param UserRoleRequest $request Validated request containing the 'role' name.
     * @param User $user The user model instance.
     * @return JsonResponse Success response if revoked, or 404 if the user didn't have the role.
     */
    public function revoke(UserRoleRequest $request, User $user): JsonResponse
    {
        $validatedData = $request->validated();
        $roleName = $validatedData['role'];

        $updatedUser = $this->roleService->revokeRoleFromUser($user, $request->validated()['role']);

        if (!$updatedUser) {
            return $this->errorResponse("User does not have the '{$roleName}' role to revoke.", null, Response::HTTP_NOT_FOUND); // 404
        }

        return $this->successResponse(
            "Role '{$roleName}' revoked from user successfully.",
            $updatedUser
        );
    }
}
