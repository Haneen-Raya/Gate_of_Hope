<?php

namespace Modules\HumanResources\Http\Requests\V1\Profession;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @class StoreProfessionRequest
 * 
 * * * Data validation and authorization layer for creating new Professions.
 * * Ensures strict adherence to organizational taxonomy standards by:
 * 1. **Data Integrity:** Enforcing string constraints and length limitations on nomenclature.
 * 2. **Uniqueness Enforcement:** Preventing redundant profession entries via database-level checks.
 * 3. **Type Safety:** Guaranteeing that operational status flags are handled as boolean primitives.
 */
class StoreProfessionRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * * Currently defaults to true; integrate with Spatie Permissions or Gate logic for production.
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
     * - **name:** Required field. Must be unique in the 'professions' table to prevent duplicate roles.
     * - **is_active:** Optional. Defaults to true if not provided, but must be boolean if present.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:professions,name',
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
