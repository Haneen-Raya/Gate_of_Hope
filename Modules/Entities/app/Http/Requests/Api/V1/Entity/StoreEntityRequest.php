<?php

namespace Modules\Entities\Http\Requests\Api\V1\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Entities\Enums\EntityType;

/**
 * Class StoreEntityRequest
 * * Handles the validation and authorization logic for creating a new organizational entity.
 * It ensures that only authorized personnel can register entities and validates
 * functional capabilities (services, referrals, funding).
 */
class StoreEntityRequest extends FormRequest
{
    /**
     * Determine if the user has the administrative privilege to create entities.
     * * @return bool
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && $user->can('entities.create');
    }

    /**
     * Get the validation rules for entity creation.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255', 'unique:entities,name'],
            'user_id'               => ['required', 'integer', 'exists:users,id'],
            'entity_type'           => ['required', 'string', Rule::in(EntityType::all())],
            'can_provide_services'  => ['sometimes', 'boolean'],
            'can_receive_referrals' => ['sometimes', 'boolean'],
            'can_fund_programs'     => ['sometimes', 'boolean'],
            'contact_person'        => ['required', 'string', 'max:500'],
            'address'               => ['required', 'string', 'max:255'],
            'is_active'             => ['sometimes', 'boolean'],
        ];
    }
}
