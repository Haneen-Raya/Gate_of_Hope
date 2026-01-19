<?php

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Services\VerificationService;


class VerificationController extends Controller
{

    /**
     * Service to handle verification-related logic 
     * and separating it from the controller
     * 
     * @var VerificationService
     */
    protected $verificationService;

    /**
     * AuthController constructor
     *
     * @param VerificationService $verificationService
     */
    public function __construct(VerificationService $verificationService)
    {
        // Inject the VerificationService to handle verification-related logic
        $this->verificationService = $verificationService;
    }

    /**
     * Send verification email to current user.
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $message = $this->verificationService->sendVerificationEmail($request->user());
        return $this->successResponse($message);
    }

    /**
     * Verify email from link.
     */
    public function verify(Request $request): JsonResponse
    {
        $id = $request->route('id');
        $message = $this->verificationService->verify($id);
        return $this->successResponse($message);
    }
}
