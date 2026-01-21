<?php

namespace Modules\Assessments\Http\Requests\V1\PriorityRule;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Assessments\Enums\PriorityLevel;

class UpdatePriorityRuleRequest extends FormRequest
{
    public function authorize() {
            return true;
        }
    public function rules(): array
    {
        return [

            'issue_type_id' => 'sometimes|exists:issue_types,id',
            'min_score'     => 'sometimes|integer|min:0',
            'max_score'     => 'sometimes|integer|gt:min_score',
            'priority'      => ['sometimes', new Enum(PriorityLevel::class)],
            'is_active'     => 'sometimes|boolean'
        ];
    }
}

?>
