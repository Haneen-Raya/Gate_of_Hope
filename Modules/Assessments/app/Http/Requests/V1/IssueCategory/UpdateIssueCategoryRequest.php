<?php

namespace Modules\Assessments\Http\Requests\V1\IssueCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }
}
