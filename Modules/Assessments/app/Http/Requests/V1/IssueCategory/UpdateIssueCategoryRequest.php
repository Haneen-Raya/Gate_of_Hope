<?php

namespace Modules\Assessments\Http\Requests\V1\IssueCategory;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateIssueCategoryRequest
 * * Handles the validation logic for updating an existing Issue Category.
 * Uses 'sometimes' rule to allow partial updates (Patch) while maintaining
 * strict data types for the provided fields.
 * * @package Modules\Assessments\Http\Requests\V1\IssueCategory
 */
class UpdateIssueCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to perform this action.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * RULES EXPLANATION:
     * - 'sometimes': The field is only validated if it is present in the input array.
     * - Supports partial updates for specific language keys.
     * * @return array<string, array|string>
     */
    public function rules(): array
    {
        return [
            // Name updates (Optional and partial)
            'name' => ['sometimes', 'array'],
            'name.ar' => ['sometimes', 'string', 'max:255'],
            'name.en' => ['sometimes', 'string', 'max:255'],

            // Label updates (Optional and partial)
            'label' => ['sometimes', 'array'],
            'label.ar' => ['sometimes', 'string', 'max:255'],
            'label.en' => ['sometimes', 'string', 'max:255'],

            // Optional status update
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
