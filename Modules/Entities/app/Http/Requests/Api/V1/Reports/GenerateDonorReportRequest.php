<?php

namespace Modules\Entities\Http\Requests\Api\V1\Reports;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GenerateDonorReportRequest
 * * Defines the criteria and constraints for triggering the donor report generation engine.
 * This request encapsulates the necessary parameters to filter program data and
 * aggregate it into a structured donor-specific snapshot.
 * * @package Modules\Entities\Http\Requests\Api\V1\Reports
 */
class GenerateDonorReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * @note Initial access is allowed at the request level, while granular
     * ownership and permission checks are deferred to the Controller/Policy layer.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the report generation request.
     * * Ensures that the report is bound to existing business entities and that
     * the temporal boundaries (reporting periods) are logically consistent.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'donor_entity_id'        => ['required', 'integer', 'exists:entities,id'],
            'program_id'             => ['required', 'integer', 'exists:programs,id'],
            'reporting_period_start' => ['required', 'date'],
            'reporting_period_end'   => ['required', 'date', 'after_or_equal:reporting_period_start'],
        ];
    }
}
