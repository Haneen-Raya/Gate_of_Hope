<?php

namespace Modules\Assessments\Http\Requests\V1\IssueCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'array'],
            'name.ar' => ['sometimes', 'string', 'max:255'],
            'name.en' => ['sometimes', 'string', 'max:255'],

            'label' => ['sometimes', 'array'],
            'label.ar' => ['sometimes', 'string', 'max:255'],
            'label.en' => ['sometimes', 'string', 'max:255'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
