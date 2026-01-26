<?php

namespace Modules\Assessments\Http\Requests\V1\GoogleForm;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateGoogleFormRequest
 *
 * Handles partial updates for existing Google Form links.
 * Uses ignore logic for the unique constraint to allow updating other fields.
 *
 * @package Modules\Assessments\Http\Requests\V1\GoogleForm
 */
class UpdateGoogleFormRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'url'           => 'sometimes|url',

            /** * Updates the issue type while ignoring the current record's ID
             * to prevent "already taken" errors during same-record updates.
             */
            'issue_type_id' => 'sometimes|exists:issue_types,id|unique:google_forms,issue_type_id,' . $this->route('google_form'),
        ];
    }

    public function messages(): array
    {
        return [
            'url.url' => 'The format of the Google Form URL is invalid.',
            'issue_type_id.unique' => 'The selected issue type is already linked to a different form.',
        ];
    }
}
