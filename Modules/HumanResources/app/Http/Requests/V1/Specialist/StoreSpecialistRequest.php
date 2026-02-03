<?php

namespace Modules\HumanResources\Http\Requests\V1\Specialist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\Gender;

/**
 * Class StoreSpecialistRequest
 * * Validates the data required to onboard a new specialist.
 * This request ensures that the specialist is correctly linked to a core User
 * and assigned to a valid technical issue category.
 */
class StoreSpecialistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to perform this action.
     * * @return bool
     */
    public function authorize(): bool
    {
        // Permission check is usually handled via middleware or policies.
        return true;
    }

    /**
     * Get the validation rules for creating a specialist profile.
     * * Key Validations:
     * - Gender: Strictly enforced via the Gender PHP Enum.
     * - User/Category: Verified against existing records in the database.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gender'            => ['required', new Enum(Gender::class)],
            'date_of_birth'     => ['required', 'date', 'before:today'],
            'user_id'           => ['required', 'exists:users,id'],
            'issue_category_id' => ['required', 'exists:issue_categories,id'],
        ];
    }
}
