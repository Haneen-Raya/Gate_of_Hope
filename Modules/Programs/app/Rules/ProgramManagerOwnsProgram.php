<?php

namespace Modules\Programs\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\Programs\Models\Program;

/**
 * Class ProgramManagerOwnsProgram
 *
 * * Purpose:
 * To enforce a strict 1-to-1 relationship check between the authenticated user
 * and the specific Program they are trying to associate with an activity.
 *
 * * Logic:
 * It intercepts the 'program_id' input and compares the 'created_by' attribute
 * of the program with the 'id' of the current session user.
 *
 * @package Modules\Programs\Rules
 */
class ProgramManagerOwnsProgram implements Rule
{
    /**
     * Determine whether the validation rule passes.
     *
     * Ensures that the program referenced by the given ID
     * is managed by the currently authenticated user.
     *
     * @param string $attribute The validated field name (e.g. program_id)
     * @param mixed  $value     The submitted program ID
     *
     * @return bool True if the user owns/manages the program, otherwise false
     */
    public function passes($attribute, $value)
    {
        $program = Program::findOrfail($value);

        if (!$program) {
            return false; // program not found
        }

        // Ensure the authenticated user is the program manager (creator)
        // This is a critical security check for multi-tenant data integrity.
        return $program->created_by === Auth::id();
    }

    /**
     * Get the validation error message.
     *
     * * Security Note:
     * Returning an unauthorized message rather than a "not found" message
     * provides better feedback for internal staff usage.
     *
     * @return string
     */
    public function message()
    {
        return 'You are not authorized to create an activity for this program.';
    }
}
