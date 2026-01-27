<?php

namespace Modules\Assessments\Http\Requests\V1\PriorityRule;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Assessments\Enums\PriorityLevel;

/**
 * Class StorePriorityRuleRequest
 * * Defines the validation logic for creating automated priority assignment rules.
 * Each rule maps a score range (min/max) to a specific PriorityLevel.
 *
 * @package Modules\Assessments\Http\Requests\V1\PriorityRule
 */
class StorePriorityRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            /** Associated issue category for this rule. */
            'issue_type_id' => 'required|exists:issue_types,id',

            /** Minimum score required to trigger this priority (must be 0 or more). */
            'min_score'     => 'required|integer|min:0',

            /** Maximum score limit. Must strictly be greater than the min_score. */
            'max_score'     => 'required|integer|gt:min_score',

            /** The priority level to be assigned if the score falls within this range. */
            'priority'      => ['required', Rule::in(PriorityLevel::all())],

            /** Toggle whether the rule is currently being applied by the engine. */
            'is_active'     => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'max_score.gt' => 'The maximum score must be higher than the minimum score.',
            'priority.in'  => 'Please select a valid priority level (Low, Medium, High, or Critical).',
        ];
    }
}
