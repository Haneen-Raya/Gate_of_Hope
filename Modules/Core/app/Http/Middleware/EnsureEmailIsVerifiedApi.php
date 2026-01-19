<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\EmailNotVerifiedException;

/**
 * API middleware to ensure the authenticated user has verified their email address
 *
 * This should be applied to API routes that require a user to have a confirmed
 * email. If the check fails, it throws a custom exception that can be
 * rendered into a JSON error response
 */
class EnsureEmailIsVerifiedApi
{
    /**
     * Handle the incoming request and verify the user's email
     *
     * @throws \Modules\Core\Exceptions\EmailNotVerifiedException If the user is not present or their email is not verified
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the currently authenticated user
        $user = auth()->user();

        // If there's no user or they haven't verified their email, block the request
        if (!$user || !$user->hasVerifiedEmail()) {
            // Throw a specific exception for the API context
            throw new EmailNotVerifiedException($user);
        }

        return $next($request);
    }
}