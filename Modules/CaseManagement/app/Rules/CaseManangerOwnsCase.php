<?php

namespace Modules\CaseManagement\Rules;


use Illuminate\Contracts\Validation\Rule;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Illuminate\Support\Facades\Auth;

class CaseManagerOwnsCase implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute  // Field name (beneficiary_case_id)
     * @param  mixed  $value       // Sent value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $case = BeneficiaryCase::find($value);

        if (!$case) {
            return false; // case not found
        }

        //Verify that the user is the case manager
        return $case->case_manager_id === Auth::id();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You can only create referrals for cases you manage.';
    }
}
