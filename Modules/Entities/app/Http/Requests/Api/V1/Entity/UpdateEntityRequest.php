<?php

namespace Modules\Entities\Http\Requests\Api\V1\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Entities\Enums\EntityType;

/**
 * Class UpdateEntityRequest
 * * Manages partial updates for existing entities.
 * This request leverages policy-based authorization to ensure that even if a user
 * has update permissions, they can only modify entities they are authorized to manage.
 */
class UpdateEntityRequest extends FormRequest
{
    /**
     * Authorize the update against the specific entity instance.
     * * Uses Route Model Binding to fetch the 'entitiy' and check the 'update' policy.
     * * @return bool
     */
    public function authorize(): bool
    {
        $entity = $this->route('entitiy');
        return $this->user()->can('update', $entity);
    }

    /**
     * Get validation rules for entity updates.
     * All fields are nullable/sometimes to support partial PATCH requests.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => ['nullable', 'string', 'max:255'],
            'entity_type'           => ['nullable', 'string', Rule::in(EntityType::all())],
            'can_provide_services'  => ['nullable', 'boolean'],
            'can_receive_referrals' => ['nullable', 'boolean'],
            'can_fund_programs'     => ['nullable', 'boolean'],
            'contact_person'        => ['nullable', 'string', 'max:500'], // تم تصحيح النوع من integer لـ string بناءً على المنطق
            'address'               => ['nullable', 'string', 'max:255'], // تم تصحيح النوع من integer لـ string
            'is_active'             => ['nullable', 'boolean'],
        ];
    }
}
