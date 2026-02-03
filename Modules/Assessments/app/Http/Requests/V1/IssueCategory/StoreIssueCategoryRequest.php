<?php

namespace Modules\Assessments\Http\Requests\V1\IssueCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            'label' => ['required', 'array'],
            'label.ar' => ['required', 'string', 'max:255'],
            'label.en' => ['required', 'string', 'max:255'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
