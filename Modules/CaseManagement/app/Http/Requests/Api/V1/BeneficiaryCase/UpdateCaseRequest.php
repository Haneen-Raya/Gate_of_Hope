<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\CaseManagement\Enums\CaseStatus;

/**
 * Class UpdateCaseRequest
 * * Handles validation logic for updating an existing beneficiary case.
 * Supports partial updates (patching) and conditional validation for closure details.
 * * @package Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase
 */
class UpdateCaseRequest extends FormRequest
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
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_manager_id' => 'sometimes|exists:users,id',
            'region_id'       => 'sometimes|exists:regions,id',
            'status'          => ['sometimes', Rule::in(CaseStatus::all())],
            'priority'        => 'sometimes|string|in:Low,Medium,High,Critical',
            'closed_at'       => 'nullable|date',
            'closure_reason'  => [
                'nullable',
                'string',
                'max:1000',
                Rule::requiredIf(fn() => !empty($this->closed_at)),
            ],
            'opened_at'       => 'sometimes|date',
        ];
    }

    /**
     * Get custom error messages for defined validation rules.
     * * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'closure_reason.required_if' => 'A closure reason is mandatory when providing a case closure date.',
            'priority.in' => 'The priority must be one of the following: Low, Medium, High, or Critical.',
            'status.in' => 'The selected status is invalid. Please use a predefined case status.',
            'exists' => 'The selected :attribute is invalid or does not exist in our records.',
        ];
    }
}
