<?php

namespace Modules\HumanResources\Http\Requests\V1\Profession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @class UpdateProfessionRequest
 * 
 * * * Data validation and authorization layer for modifying existing Professions.
 * * This request ensures that any structural changes to the profession taxonomy
 * maintain system integrity by:
 * 1. **Partial Update Support:** Utilizing 'sometimes' to allow for granular field modifications.
 * 2. **Conflict Prevention:** Enforcing uniqueness on the name while explicitly ignoring 
 * the current resource ID during the validation cycle.
 * 3. **Operational Logic:** Ensuring status toggles conform to boolean primitives for 
 * consistent state management.
 */
class UpdateProfessionRequest extends FormRequest
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
     * * * * Validation Logic:
     * - **name:** Optional but must be unique if provided. The current profession ID 
     * is ignored to prevent "name already taken" errors during self-updates.
     * - **is_active:** Optional. Must be a boolean value (true/false/1/0).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('professions', 'name')->ignore($this->profession->id),
            ],
            'is_active' => 'sometimes|boolean'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'      => 'Profession Name',
            'is_active' => 'Active Status',
        ];
    }
}
