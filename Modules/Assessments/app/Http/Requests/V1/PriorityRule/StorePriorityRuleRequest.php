<?php

namespace Modules\Assessments\Http\Requests\V1\PriorityRule;


use Illuminate\Validation\Rule;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Assessments\Enums\PriorityLevel;

class StorePriorityRuleRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules()
    {
        //$id = $this->route('priority_rule');
        return [
            'issue_type_id' => 'required|exists:issue_types,id',
            'min_score'     => 'required|integer|min:0',
            'max_score'     => 'required|integer|gt:min_score',
            'priority'      => ['required',Rule::in(PriorityLevel::all())],
            'is_active'     => 'boolean'
        ];
    }
}
