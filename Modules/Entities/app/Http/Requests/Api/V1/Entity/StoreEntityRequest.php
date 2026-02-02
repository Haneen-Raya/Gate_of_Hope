<?php

namespace Modules\Entities\Http\Requests\Api\V1\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Entities\Enums\EntityType;

class StoreEntityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('entities.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255','unique:entities,name'],
            'user_id'               => ['required','integer','exists:users,id'],
            'entity_type'           => ['required','string',Rule::in(EntityType::all())],
            'can_provide_services'  => ['sometimes','boolean'],
            'can_receive_referrals' => ['sometimes','boolean'],
            'can_fund_programs'     => ['sometimes','boolean'],
            'contact_person'        => ['required', 'string', 'max:500'],
            'address'               => ['required', 'string', 'max:255'],
            'is_active'             => ['sometimes','boolean'],
        ];
    }
}
