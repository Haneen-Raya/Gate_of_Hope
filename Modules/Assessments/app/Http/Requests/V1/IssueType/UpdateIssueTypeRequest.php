<?php

namespace Modules\Assessments\Http\Requests\V1\IssueType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'issue_category_id' => ['sometimes', 'required', 'exists:issue_categories,id'],

            // Name can be updated partially
            'name' => ['sometimes', 'array'],
            'name.ar' => ['sometimes', 'string', 'max:255'],
            'name.en' => ['sometimes', 'string', 'max:255'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
