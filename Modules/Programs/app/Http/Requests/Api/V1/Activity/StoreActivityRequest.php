<?php

namespace Modules\Programs\Http\Requests\Api\V1\Activity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\Api\V1\Activity\ActivityType;

class StoreActivityRequest extends FormRequest
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
            'program_id'         => ['required', 'integer', 'exists:programs,id'],
            'profession_id'      => ['required', 'integer', 'exists:professions,id'],
            'provider_entity_id' => ['required', 'integer', 'exists:entities,id'],
            'activity_type'      => ['required', 'string', Rule::in(ActivityType::all())],
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:1000'],
        ];
    }
}
