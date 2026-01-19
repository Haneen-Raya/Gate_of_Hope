<?php

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Requests\Auth\ResetPasswordRequest;
use Modules\Core\Services\PasswordResetService;

class ResetPasswordController extends Controller
{
    /**
     * Service to handle resetPassword-related logic 
     * and separating it from the controller
     * @var PasswordResetService
     */
    protected PasswordResetService $passwordResetService;

    /**
     * ResetPasswordController constructor
     *
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(PasswordResetService $passwordResetService)
    {
        // Inject the ResetPasswordController to handle resetPassword-related logic
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Handle the request to reset the user's password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $this->passwordResetService->reset(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );
        return $this->successResponse('Password has been reset successfully');
    }
}
