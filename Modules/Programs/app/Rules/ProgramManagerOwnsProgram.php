<?php

namespace Modules\Programs\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\Programs\Models\Program;

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
        return $program->created_by === Auth::id();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are not authorized to create an activity for this program.';
    }
}
