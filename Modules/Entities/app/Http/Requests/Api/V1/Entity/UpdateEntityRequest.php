<?php

namespace Modules\Entities\Http\Requests\Api\V1\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Entities\Enums\EntityType;

class UpdateEntityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                  => ['nullable', 'string', 'max:255'],
            'entity_type'           => ['nullable','string',Rule::in(EntityType::all())],
            'can_provide_services'  => ['nullable','boolean'],
            'can_receive_referrals' => ['nullable','boolean'],
            'can_fund_programs'     => ['nullable','boolean'],
            'contact_person'        => ['nullable', 'integer', 'max:500'],
            'address'               => ['nullable', 'integer', 'max:255'],
            'is_active'             => ['nullable','boolean'],
        ];
    }
}
