<?php

namespace Modules\Core\Http\Requests\V1\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UserRoleRequest
 * * Handles validation and authorization for user role-related requests.
 * This request ensures that only authorized admins can modify roles and that the provided role exists.
 */
class UserRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * * Current Logic: Only users with the 'Super Admin' role are allowed.
     *
     * @return bool Returns true if the user is authorized, false otherwise.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin') ;
    }

    /**
     * Get the validation rules that apply to the request.
     * * * Validates:
     * * 'role': Must be present, a string, and exist in the 'name' column of the 'roles' table.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name'),
            ],
        ];
    }
}
