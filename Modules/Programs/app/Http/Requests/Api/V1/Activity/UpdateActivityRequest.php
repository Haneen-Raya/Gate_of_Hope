<?php

namespace Modules\Programs\Http\Requests\Api\V1\Activity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\Api\V1\Activity\ActivityType;

class UpdateActivityRequest extends FormRequest
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
            'program_id'         => ['nullable', 'integer', 'exists:programs,id'],
            'profession_id'      => ['nullable', 'integer', 'exists:professions,id'],
            'provider_entity_id' => ['nullable', 'integer', 'exists:entities,id'],
            'activity_type'      => ['nullable', 'string', Rule::in(ActivityType::all())],
            'name'               => ['nullable', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'is_active'          => ['nullable','boolean'],
        ];
    }
}

