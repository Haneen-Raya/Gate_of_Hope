<?php

namespace Modules\Assessments\Http\Requests\V1\IssueType;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'issue_category_id' => ['required', 'exists:issue_categories,id'],

            // Name is multilingual
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
