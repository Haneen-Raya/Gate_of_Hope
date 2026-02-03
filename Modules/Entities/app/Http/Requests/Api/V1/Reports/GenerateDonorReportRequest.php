<?php

namespace Modules\Entities\Http\Requests\Api\V1\Reports;

use Illuminate\Foundation\Http\FormRequest;

class GenerateDonorReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

      /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'donor_entity_id' => 'required|integer|exists:entities,id',
            'program_id'      => 'required|integer|exists:programs,id',
            'reporting_period_start' => 'required|date',
            'reporting_period_end'   => 'required|date|after_or_equal:reporting_period_start',
        ];
    }
}
