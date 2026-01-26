<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CaseReview;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\V1\ProgressStatus;

/**
 * @class UpdateCaseReviewRequest
 * 
 * * Responsible for managing partial updates to existing Case Reviews.
 * It enforces data integrity by preventing the relocation of reviews between cases
 * while allowing specialists to refine progress assessments and clinical notes.
 */
class UpdateCaseReviewRequest extends FormRequest
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
     * - **Immutability Constraint:** Uses 'prohibited' for 'beneficiary_case_id' to ensure 
     * a review remains permanently attached to its original case, preventing audit trail distortion.
     * - **Partial Updates (PATCH):** Employs 'sometimes' to allow updating only the 
     * necessary fields without requiring a full payload.
     * - **Clinical Refinement:** Permits updates to notes and progress status 
     * to reflect corrections or additional insights from the specialist.
     * 
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'beneficiary_case_id' => 'prohibited',
            'progress_status'     => ['sometimes', Rule::in(ProgressStatus::all())],
            'notes'               => 'sometimes|nullable|string|max:2000',
        ];
    }

    /**
     * Custom attribute names for user-friendly validation error messages.
     * * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'progress_status' => 'Progress Status',
            'notes'           => 'Specialist Notes',
        ];
    }
}
