<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\CaseManagement\Enums\CaseStatus;

/**
 * Class StoreCaseRequest
 * * Handles validation logic for creating a new beneficiary case.
 * Ensures all required relational IDs exist and status conforms to CaseStatus enum.
 * * @package Modules\CaseManagement\Http\Requests\Api\V1\BeneficiaryCase
 */
class StoreCaseRequest extends FormRequest
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
            'beneficiary_id'  => 'required|exists:beneficiaries,id',
            'issue_type_id'   => 'required|exists:issue_types,id',
            'case_manager_id' => 'required|exists:users,id',
            'region_id'       => 'required|exists:regions,id',
            'status'          => ['required', Rule::in(CaseStatus::all())],
            'priority'        => 'required|string',
            'opened_at'       => 'required|date',
        ];
    }
    /**
     * Get custom error messages for defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'beneficiary_id.required'  => 'Please select a beneficiary to open a case.',
            'issue_type_id.required'   => 'An issue type must be specified for the new case.',
            'case_manager_id.required' => 'A case manager must be assigned to this case.',
            'region_id.required'       => 'The region field is required.',
            'status.in'                => 'The selected status is invalid. Please use a standard case status.',
            'opened_at.required'       => 'The case opening date is required.',
            'exists'                   => 'The selected :attribute does not exist in our system.',
        ];
    }
}
