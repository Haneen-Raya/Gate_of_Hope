<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CaseReview;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\V1\ProgressStatus;

/**
 * @class StoreCaseReviewRequest
 * 
 * * Responsible for validating periodic case evaluation data.
 * It ensures that each review is linked to a legitimate beneficiary case,
 * adheres to standardized progress metrics, and maintains chronological accuracy.
 */
class StoreCaseReviewRequest extends FormRequest
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
     * - **Referential Integrity:** Confirms the existence of the beneficiary case in the system.
     * - **Metric Standardization:** Restricts 'progress_status' to values defined in ProgressStatus Enum
     * to ensure data consistency for analytical reporting.
     * - **Temporal Accuracy:** Prevents future-dated reviews by enforcing 'before_or_equal:now', 
     * ensuring the audit trail reflects past or present actions only.
     * - **Data Quality:** Sets a reasonable ceiling for descriptive notes to maintain database performance.
     * 
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'beneficiary_case_id' => 'required|exists:beneficiary_cases,id',
            'progress_status' => [
                'required',
                'string',
                Rule::in(ProgressStatus::all())
            ],

            'notes' => 'nullable|string|max:1000',

            'reviewed_at' => 'required|date|before_or_equal:now',
        ];
    }

    /**
     * Custom attribute names for user-friendly validation error messages.
     * * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'beneficiary_case_id' => 'Beneficiary Case',
            'progress_status'     => 'Progress Trajectory',
            'reviewed_at'         => 'Review Date',
            'notes'               => 'Clinical/Social Notes',
        ];
    }
}
