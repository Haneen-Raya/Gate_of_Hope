<?php

namespace Modules\Assessments\Http\Requests\V1\IssueCategory;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreIssueCategoryRequest
 * * Handles the validation logic for creating a new Issue Category.
 * This request ensures that multi-lingual data (Arabic & English) is provided
 * for both 'name' and 'label' fields to maintain localization integrity.
 * * @package Modules\Assessments\Http\Requests\V1\IssueCategory
 */
class StoreIssueCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to perform this action.
     * Typically integrates with Laravel's Gate or Policy system.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * EXPECTED PAYLOAD STRUCTURE:
     * {
     * "name": {"ar": "اسم التصنيف", "en": "Category Name"},
     * "label": {"ar": "تسمية العرض", "en": "Display Label"},
     * "is_active": true
     * }
     * * @return array<string, array|string>
     */
    public function rules(): array
    {
        return [
            // Multi-lingual name validation
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            // Multi-lingual label validation for UI display
            'label' => ['required', 'array'],
            'label.ar' => ['required', 'string', 'max:255'],
            'label.en' => ['required', 'string', 'max:255'],

            // Optional status flag
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
