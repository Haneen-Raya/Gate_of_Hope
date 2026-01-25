<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @class UpdateCaseSupportPlanRequest
 * 
 * Manages the validation logic for updating existing Case Support Plans.
 * It employs a "Patch-friendly" strategy, allowing optional updates to 
 * specific fields while maintaining strict temporal and logical constraints.
 */
class UpdateCaseSupportPlanRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Validation Strategy:
     * - **Conditional Presence:** Uses 'sometimes' to allow partial updates (PATCH style), 
     * meaning rules are only applied if the field is present in the request.
     * - **Temporal Consistency:** Even in updates, the start date cannot be backdated 
     * to the past, and the end date must logically succeed the start date.
     * - **Boolean Integrity:** Ensures the activation status is strictly castable to boolean.
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_active' => 'sometimes|boolean',
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after:start_date',
        ];
    }

    /**
     * Custom attribute names for semantic error reporting.
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'is_active'  => 'Activation Status',
            'start_date' => 'Commencement Date',
            'end_date'   => 'Completion Date',
        ];
    }
}
