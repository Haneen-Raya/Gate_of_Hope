<?php

namespace Modules\Assessments\Http\Requests\V1\IssueType;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateIssueTypeRequest
 *
 * Validation layer for modifying existing Issue Types. Supports partial updates
 * (PATCH) for category reassignment, localized name changes, or status toggling.
 *
 * @package Modules\Assessments\Http\Requests\V1\IssueType
 */
class UpdateIssueTypeRequest extends FormRequest
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
     *
     * UPDATE LOGIC:
     * - All fields are 'sometimes', allowing the client to update only what is necessary.
     * - Reassigning a category still requires the ID to exist in the database.
     *
     * @return array<string, array|string>
     */
    public function rules(): array
    {
        return [
            // Optional category reassignment
            'issue_category_id' => ['sometimes', 'required', 'exists:issue_categories,id'],

            // Partial or full multilingual name updates
            'name' => ['sometimes', 'array'],
            'name.ar' => ['sometimes', 'string', 'max:255'],
            'name.en' => ['sometimes', 'string', 'max:255'],

            // Optional status toggle
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
