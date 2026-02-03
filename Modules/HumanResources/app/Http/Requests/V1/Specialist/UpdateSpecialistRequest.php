<?php

namespace Modules\HumanResources\Http\Requests\V1\Specialist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\V1\Gender;

/**
 * Class UpdateSpecialistRequest
 * * Handles partial updates for a specialist's professional profile.
 * Uses the 'sometimes' rule to allow updating specific fields without
 * requiring the full dataset.
 */
class UpdateSpecialistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to update this specialist.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for updating a specialist.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gender'            => ['sometimes', new Enum(Gender::class)],
            'date_of_birth'     => ['sometimes', 'date', 'before:today'],
            'user_id'           => ['sometimes', 'exists:users,id'],
            'issue_category_id' => ['sometimes', 'exists:issue_categories,id'],
        ];
    }
}
