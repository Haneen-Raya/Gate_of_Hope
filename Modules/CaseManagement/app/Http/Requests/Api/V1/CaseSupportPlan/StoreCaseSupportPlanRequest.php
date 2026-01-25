<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CaseSupportPlan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @class StoreCaseSupportPlanRequest
 * 
 * Responsible for validating the lifecycle data of a Case Support Plan.
 * It ensures referential integrity with beneficiary cases and enforces 
 * strict chronological rules for the plan's duration.
 */
class StoreCaseSupportPlanRequest extends FormRequest
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
     * - Referential Integrity: Ensures the 'beneficiary_case_id' exists in the database.
     * - Versioning Control: Validates that the version is a positive incremental integer.
     * - Temporal Constraints: Enforces that the plan cannot start in the past 
     * and must have a logical end date following the start date.
     * 
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'beneficiary_case_id' => 'required|exists:beneficiary_cases,id',
            'version' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    /**
     * Custom attribute names for user-friendly validation error messages.
     * * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'beneficiary_case_id' => 'Beneficiary Case Reference',
            'is_active'           => 'Activation Status',
            'start_date'          => 'Commencement Date',
            'end_date'            => 'Completion Date',
        ];
    }
}
