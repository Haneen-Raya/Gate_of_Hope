<?php

namespace Modules\Assessments\Http\Requests\V1\IssueType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Category can be changed, but must exist
            'issue_category_id' => 'sometimes|required|exists:issue_categories,id',

            // Name can be updated
            'name' => 'sometimes|required|string|max:255',

            // Optional active flag
            'is_active' => 'sometimes|boolean',
        ];
    }
}
