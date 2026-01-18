<?php

namespace Modules\Core\Services;

use App\Facades\Logger;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\User;

/**
 * Service class for handling all user authentication logic
 *
 * This service manages user registration, login, and logout operations
 * It uses a central `handle` method to ensure consistent error handling
 * and logging for all auth-related actions
 */
class AuthService
{
    /**
     * A central error handler for authentication operations
     *
     * This wrapper provides a consistent try/catch block for auth methods,
     * so we don't have to repeat the same error handling logic everywhere
     * It catches specific exceptions and re-throws them in a standard format
     *
     * @param Closure $callback The function containing the core logic to execute
     * @return mixed The result from the callback function
     * @throws \Illuminate\Auth\AuthenticationException If credentials are bad
     * @throws \Exception For any other unexpected errors
     */
    protected function handle(Closure $callback)
    {
        try {
            // Run the logic passed into the handler
            return $callback();
        } catch (AuthenticationException $e) {
            // Catch a specific framework auth exception
            report($e);
            // Re-throw it with a generic, user-friendly message
            throw new AuthenticationException('Invalid credentials');
        } catch (\Throwable $e) {

            if (config('app.debug', false)) {

                $detailedMessage = sprintf(
                    "Error: %s in %s on line %d",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),

                );
                throw new \Exception($detailedMessage, 500);
            }
            throw new \Exception('An internal server error occurred', 500);
        }
    }

    /**
     * Handle a new user registration
     *
     * Hashes the password, creates the user, generates an API token,
     * logs the event, and fires the 'Registered' event for other listeners
     *
     * @param array $data User data (name, email, password)
     * @return array|bool An array with the user's name and token on success, or false on failure
     */
    public function register(array $data)
    {
        return $this->handle(function () use ($data) {
            // hash the password before saving it
            $data['password'] = Hash::make($data['password']);

            // Create the user without triggering activity logs for the creation itself
            $user = activity()
                ->withoutLogs(function () use ($data) {
                    return User::create($data);
                });

            // Make sure the user was actually created before proceeding
            if ($user->wasRecentlyCreated) {
                // Prepare the success response
                $success['name'] = $user->name;
                // Create a Sanctum token that expires in one week
                $success['token'] = $user->createToken('MyApp', ['*'], now()->addWeek())->plainTextToken;

                // Log the successful registration for auditing
                Logger::auth('registered', 'the user has been registered successfully', [
                    'user' => $user,
                ], $user);

                // Fire the built-in Laravel event
                event(new Registered($user));

                return $success;
            }

            // Return false if for some reason the user wasn't created
            return false;
        });
    }

    /**
     * Handle a user login attempt
     *
     * Tries to authenticate the user with email and password
     * If successful, it creates a new API token and logs the login
     *
     * @param array $data User credentials (email, password)
     * @return array|bool An array with the user object and token, or false on failure
     */
    public function login(array $data)
    {
        return $this->handle(function () use ($data) {
            // Attempt to authenticate
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                $user = Auth::user();
                $token = $user->createToken('MyApp', ['*'], now()->addWeek())->plainTextToken;

                // Log the successful login
                Logger::auth('login', 'the user has been Logged in successfully', [
                    'user' => $user,
                ], $user);

                return [
                    'user' => $user,
                    'token' => $token,
                ];
            }

            // If auth fails, log the attempt for security monitoring
            Logger::auth('failed-login', 'An user could not Login!', [
                'email' => $data['email'],
            ]);

            return false;
        });
    }

    /**
     * Log the current user out by revoking their API token
     *
     * @return bool True if the token was deleted successfully
     */
    public function logout()
    {
        return $this->handle(function () {
            $user = auth()->user();

            // Log the logout event before revoking the token
            Logger::auth('logout', 'An user has been logged out successfully', [
                'user' => $user,
            ]);

            // Delete the specific token that was used for the current request
            return $user->currentAccessToken()->delete();
        });
    }
}
