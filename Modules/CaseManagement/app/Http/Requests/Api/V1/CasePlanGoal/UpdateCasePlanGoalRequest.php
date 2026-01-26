<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CasePlanGoal;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\V1\PlanStatus;
use Modules\CaseManagement\Rules\ValidGoalTargetDate;

/**
 * @class UpdateCasePlanGoalRequest
 * 
 * * Manages the validation and data preparation for updating existing Goal objectives.
 * It features "Auto-Sync" logic for achievement timestamps and enforces
 * strict temporal alignment with the parent support plan.
 */
class UpdateCasePlanGoalRequest extends FormRequest
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
     * Prepare the data for validation.
     * 
     * * * Business Logic:
     * - **Automated Timestamping:** If the goal status is transitioned to 'achieved', 
     * the system automatically injects the current date into 'achieved_at'.
     * This ensures data integrity without requiring manual input from the officer.
     */
    protected function prepareForValidation()
    {
        if ($this->input('status') == PlanStatus::ACHIEVED->value) {
            $this->merge([
                'achieved_at' => Carbon::today()->toDateString()
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * * Validation Strategy:
     * - **Partial Updates (PATCH):** Uses 'sometimes' to allow updating specific fields.
     * - **Cross-Model Consistency:** Re-validates the 'target_date' against the 
     * parent plan's duration using the ValidGoalTargetDate custom rule.
     * - **Achievement Guard:** Ensures that 'achieved_at', if provided or merged, 
     * aligns strictly with the day of the operation.
     * 
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'goal_description' => 'sometimes|string|max:1000',
            'status' => ['sometimes', Rule::in(PlanStatus::all())],
            'target_date' => ['sometimes', 'date', 'after:today', new ValidGoalTargetDate($this->route('case_plan_goal')->plan_id)],
            'notes' => 'sometimes|nullable|string|max:1000',
            'achieved_at' => 'sometimes|nullable|date|date_equals:today'
        ];
    }

    /**
     * Custom attribute names for user-friendly validation error messages.
     * * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'goal_description' => 'Goal Description',
            'status'           => 'Current Status',
            'target_date'      => 'Target Completion Date',
            'achieved_at'      => 'Achievement Date',
        ];
    }
}
