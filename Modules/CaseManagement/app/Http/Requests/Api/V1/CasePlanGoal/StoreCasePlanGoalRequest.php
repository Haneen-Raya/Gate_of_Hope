<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CasePlanGoal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\V1\PlanStatus;
use Modules\CaseManagement\Rules\ValidGoalTargetDate;

/**
 * @class StoreCasePlanGoalRequest
 * 
 * * Handles the validation for creating specific objectives within a support plan.
 * It ensures that every goal is linked to a valid parent plan, adheres to 
 * domain-specific statuses, and maintains a proactive timeline.
 */
class StoreCasePlanGoalRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * * Validation Strategy:
     * - **Relational Integrity:** Validates that 'plan_id' exists in the case_support_plans table.
     * - **Domain Integrity:** Enforces the 'status' field using the PlanStatus Enum to ensure 
     * consistency across the case management lifecycle.
     * - **Temporal Discipline:** Mandates that the 'target_date' is set in the future, 
     * preventing the creation of expired objectives.
     * - **Data Quality:** Implements length constraints on text fields to prevent payload bloating.
     * 
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan_id' => 'required|exists:case_support_plans,id',
            'goal_description' => 'required|string|max:1000',
            'status' => ['required', Rule::in(PlanStatus::all())],
            'target_date' => ['sometimes', 'date', 'after:today', new ValidGoalTargetDate($this->plan_id)],
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom attribute names for user-friendly validation error messages.
     * * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'target_date' => 'Target Completion Date',
        ];
    }
}
