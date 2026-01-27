<?php

namespace Modules\Assessments\Http\Requests\V1\GoogleForm;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreGoogleFormRequest
 *
 * Handles validation for linking a new Google Form to a specific issue type.
 * Ensures that each issue type has only one unique Google Form.
 *
 * @package Modules\Assessments\Http\Requests\V1\GoogleForm
 */
class StoreGoogleFormRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            /** The full URL of the Google Form. Must be a valid URL format. */
            'url'           => 'required|url',

            /** * The associated issue type.
             * Must exist in the database and must not already be linked to another form.
             */
            'issue_type_id' => 'required|exists:issue_types,id|unique:google_forms,issue_type_id'
        ];
    }

    /**
     * Custom messages for validation errors in English.
     */
    public function messages(): array
    {
        return [
            'issue_type_id.unique' => 'This issue type already has an assigned Google Form.',
            'url.url' => 'Please provide a valid Google Form URL.',
        ];
    }
}
