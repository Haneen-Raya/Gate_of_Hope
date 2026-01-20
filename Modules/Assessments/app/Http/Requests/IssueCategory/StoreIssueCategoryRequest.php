<?php

namespace Modules\Assessments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:issue_categories,code',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
      return auth()->user()->can('manage_issue_categories');
    }

}
