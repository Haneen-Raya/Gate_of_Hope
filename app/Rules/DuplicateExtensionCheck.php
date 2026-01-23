<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\UnauthorizedException;

/**
 * @class DuplicateExtensionCheck Rule
 *
 * A critical security-focused validation rule designed to mitigate "Double Extension Attacks".
 * 
 * Logic:
 * - It scans the original client filename for multiple periods (e.g., shell.php.jpg).
 * - Attackers often use this technique to bypass MIME checks and execute malicious 
 * scripts on the server if the webserver is misconfigured.
 * 
 * Security Impact: 
 * If a double extension is detected, it throws an UnauthorizedException, 
 * treating the request as a potential malicious intrusion rather than a simple validation error.
 */
class DuplicateExtensionCheck implements ValidationRule
{
    /**
     * Run the validation rule.
     * Checks the file's original name for nested or multiple extensions.
     *
     * @param string $attribute The field under validation (e.g., identity_file).
     * @param mixed $value The uploaded file instance.
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @throws UnauthorizedException If a potential security bypass attempt is detected.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Regex Logic: Looks for any pattern where a dot is followed by characters then another dot.
        // Example: 'image.png' (Pass), 'virus.php.jpg' (Fail)
        if (preg_match('/\.[^.]+\./', $value->getClientOriginalName()))

            /**
             * We use UnauthorizedException (403) instead of $fail() to:
             * 1. Terminate the request immediately.
             * 2. Signal to the security monitoring system that an automated attack may be occurring.
             */
            throw new UnauthorizedException('Security Breach Attempt: Multiple file extensions are not allowed.', 403);
    }
}
