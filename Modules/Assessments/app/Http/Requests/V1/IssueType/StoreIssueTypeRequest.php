<?php

namespace Modules\Assessments\Http\Requests\V1\IssueType;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreIssueTypeRequest
 *
 * Validation layer for creating a new Issue Type. This request ensures the linkage
 * to a valid Issue Category and enforces mandatory bilingual naming.
 *
 * @package Modules\Assessments\Http\Requests\V1\IssueType
 */
class StoreIssueTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to perform this action.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * DATA REQUIREMENTS:
     * - 'issue_category_id': Must exist in the issue_categories table to maintain referential integrity.
     * - 'name': A bilingual array requiring both 'ar' and 'en' keys.
     *
     * @return array<string, array|string>
     */
    public function rules(): array
    {
        return [
            // Referential integrity check for the parent category
            'issue_category_id' => ['required', 'exists:issue_categories,id'],

            // Mandatory multilingual name validation
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            // Optional status flag (defaults to active in DB usually)
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
