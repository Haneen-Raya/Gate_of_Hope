<?php

namespace Modules\CaseManagement\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\CaseManagement\Models\CaseSupportPlan;

/**
 * @class ValidGoalTargetDate
 * 
 * * Validates that a goal's target date resides within the temporal boundaries 
 * of its parent Support Plan (between start_date and end_date).
 */
class ValidGoalTargetDate implements ValidationRule
{
    /**
     * @param int|null $planId The ID of the parent Case Support Plan.
     */
    public function __construct(protected ?int $planId) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->planId) {
            return;
        }

        // Fetch the parent plan to check its duration.
        $plan = CaseSupportPlan::find($this->planId);

        if (!$plan) {
            $fail('The selected support plan is invalid.');
            return;
        }

        $targetDate = Carbon::parse($value);

        // Logical Constraint: Target date must be >= plan start AND <= plan end.
        if ($targetDate->lt($plan->start_date) || $targetDate->gt($plan->end_date)) {
            $fail("The :attribute must be between the plan's start date ({$plan->start_date}) and end date ({$plan->end_date}).");
        }
    }
}
