<?php

namespace Modules\Assessments\Http\Requests\V1\PriorityRule;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Assessments\Enums\PriorityLevel;

/**
 * Class UpdatePriorityRuleRequest
 * * Handles partial updates for priority rules.
 * Ensures that even during updates, the logical integrity of score ranges is maintained.
 *
 * @package Modules\Assessments\Http\Requests\V1\PriorityRule
 */
class UpdatePriorityRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'issue_type_id' => 'sometimes|exists:issue_types,id',
            'min_score'     => 'sometimes|integer|min:0',
            'max_score'     => 'sometimes|integer|gt:min_score',

            /** Validates the priority using the native PHP Enum rule. */
            'priority'      => ['sometimes', new Enum(PriorityLevel::class)],

            'is_active'     => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'max_score.gt' => 'The maximum score cannot be equal to or less than the minimum score.',
        ];
    }
}
