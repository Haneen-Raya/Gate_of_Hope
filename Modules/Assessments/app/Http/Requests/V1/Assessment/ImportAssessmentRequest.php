<?php

namespace Modules\Assessments\Http\Requests\V1\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ImportAssessmentRequest
 * * Validates the spreadsheet file and associated metadata for importing assessments.
 * Supports Excel (.xlsx) and CSV formats.
 * * @package Modules\Assessments\Http\Requests\V1\Assessment
 */
class ImportAssessmentRequest extends FormRequest
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
            /** * The data file to be imported.
             * Must be an Excel or CSV file, maximum size 10MB.
             */
            'file'          => 'required|mimes:xlsx,csv|max:10240',

            /** * The issue type category to which these assessments will belong.
             * Must exist in the issue_types table.
             */
            'issue_type_id' => 'required|exists:issue_types,id'
        ];
    }

    /**
     * Custom attributes for validation errors.
     * * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'file' => 'assessment file',
            'issue_type_id' => 'issue category',
        ];
    }
}
