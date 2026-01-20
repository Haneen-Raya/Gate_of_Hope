<?php

namespace Modules\Assessments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
