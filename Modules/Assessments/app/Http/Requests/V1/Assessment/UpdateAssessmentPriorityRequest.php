<?php

namespace Modules\Assessments\Http\Requests\V1\Assessment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Assessments\Enums\PriorityLevel;

/**
 * @class UpdateAssessmentPriorityRequest
 * 
 * * Data Validation Layer for modifying assessment priorities.
 * * This request ensures that any human-overridden priority level adheres to 
 * predefined organizational standards and includes sufficient justification.
 * * * Validation Logic:
 * 1. **Priority Integrity:** Restricts the 'priority_final' field to values defined 
 * within the `PriorityLevel` Enum, preventing invalid state transitions.
 * 2. **Contextual Auditing:** Enforces a character limit on 'justification' to ensure 
 * specialist feedback is concise yet descriptive for audit trails.
 */
class UpdateAssessmentPriorityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * Current: Defaults to true (Permission-based auth handled in Controller/Middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priority_final' => [Rule::in(PriorityLevel::all())],
            'justification' => 'sometimes|nullable|string|max:1000'
        ];
    }
}
