<?php

namespace Modules\Core\Services;

use App\Facades\Logger;
use App\Services\Base\BaseService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Handles the business logic for password resets
 *
 * This service uses Laravel's built-in Password broker to manage the
 * sending of reset links and the actual password reset process,
 * adding custom logging for both success and failure cases
 */
class PasswordResetService extends BaseService
{
    /**
     * Send a password reset link to a user's email
     *
     * @param string $email The email address to send the link to
     * @return string The status message from the Password broker
     *
     * @throws \Illuminate\Validation\ValidationException If the email is invalid or sending fails
     */
    public function sendResetLink(string $email): string
    {
        // wrapping this in the parent `handle` method for consistent error handling
        return $this->handle(function () use ($email) {

            // Use the 'users' password broker to send the reset link
            $status = Password::broker('users')->sendResetLink(['email' => $email]);

            // Check if the link was NOT sent successfully
            if ($status !== Password::RESET_LINK_SENT) {
                // Log the failure for security and debugging purposes
                Logger::auth('reset-link-failed', 'Failed to send password reset link', [
                    'email' => $email,
                    'status' => $status,
                ]);

                // Throw a validation exception with the translated status message
                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }

            // Log the successful sending of the link
            Logger::auth('reset-link-sent', 'Password reset link sent successfully', [
                'email' => $email,
            ]);

            return $status;
        });
    }

    /**
     * Reset the user's password using the provided token and credentials
     *
     * @param array $credentials The data required to reset the password
     * @option string $email The user's email
     * @option string $token The password reset token from the email
     * @option string $password The new password
     * @option string $password_confirmation The new password confirmed
     *
     * @return string The status message from the Password broker
     *
     * @throws \Illuminate\Validation\ValidationException If the token or credentials are invalid
     */
    public function reset(array $credentials): string
    {
        return $this->handle(function () use ($credentials) {

            // Attempt to reset the password using the 'users' broker
            $status = Password::broker('users')->reset(
                $credentials,
                // This callback runs only if the token and user are valid
                function ($user, $password) {
                    // Force fill the new hashed password and a new remember token
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    // Log the successful password change
                    Logger::auth('password-reset', 'User password has been reset successfully', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);

                    // Fire the PasswordReset event for other listeners
                    event(new PasswordReset($user));
                }
            );

            // If the reset was not successful, handle the failure
            if ($status !== Password::PASSWORD_RESET) {
                // Log the failed attempt
                Logger::auth('password-reset-failed', 'Password reset failed', [
                    'email' => $credentials['email'],
                    'status' => $status,
                ]);

                // Throw an exception with the specific error message
                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }

            return $status;
        });
    }
}