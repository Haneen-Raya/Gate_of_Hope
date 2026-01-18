<?php

namespace Modules\Core\Services;

use App\Facades\Logger;
use App\Services\Base\BaseService;
use Illuminate\Auth\Events\Verified;
use Modules\Core\Models\User;

/**
 * Handles the business logic for user email verification
 *
 * This service is responsible for sending verification links and marking
 * users as verified when they complete the process
 */
class VerificationService extends BaseService
{
    /**
     * Send a verification email to a user if they are not already verified
     *
     * @param \Modules\Core\Models\User $user The user to send the email to
     * @return string A status message indicating the result
     */
    public function sendVerificationEmail(User $user): string
    {
        // Use the base service's error handler
        return $this->handle(function () use ($user) {
            // First, check if the user's email is already marked as verified
            if ($user->hasVerifiedEmail()) {
                return 'Email already verified';
            }

            // If not verified, send the built-in Laravel verification notification
            $user->sendEmailVerificationNotification();

            // Log the action for auditing purposes
            Logger::auth('verification-email-sent', 'A verification email was sent to a user', ['user' => $user]);

            return 'Verification link sent to your email';
        });
    }

    /**
     * Mark a user's email as verified
     *
     * This is typically called after a user clicks the verification link in their email
     *
     * @param int $id The ID of the user to verify
     * @return string A status message indicating the result
     */
    public function verify(int $id): string
    {
        return $this->handle(function () use ($id) {
            // Find the user by their ID or fail with a 404 error
            $user = User::findOrFail($id);

            // If they are already verified, no need to do anything else
            if ($user->hasVerifiedEmail()) {
                return 'Email already verified';
            }

            // Mark the user's email as verified in the database
            $user->markEmailAsVerified();
            // Fire the 'Verified' event so other parts of the app can react
            event(new Verified($user));

            // Log the successful verification
            Logger::auth('email-verified', 'An email has been verified', ['user' => $user]);

            return 'Email successfully verified';
        });
    }
}