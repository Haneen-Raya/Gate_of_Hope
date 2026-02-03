<?php

namespace Modules\Programs\Http\Requests\Api\V1\Activity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\V1\ActivityType;
use Modules\Programs\Rules\ProgramManagerOwnsProgram;

class StoreActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('activities.create');
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'program_id'         => ['required', 'integer', 'exists:programs,id', new ProgramManagerOwnsProgram()],
            'profession_id'      => ['required', 'integer', 'exists:professions,id'],
            'provider_entity_id' => ['required', 'integer', 'exists:entities,id'],
            'activity_type'      => ['required', 'string', Rule::in(ActivityType::all())],
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:1000'],
        ];
    }
}
