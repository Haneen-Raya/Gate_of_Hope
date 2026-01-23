<?php

namespace Modules\Assessments\Http\Requests\IssueType;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'issue_category_id' => 'required|exists:issue_categories,id',
            'name' => 'required|string|max:255',
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
