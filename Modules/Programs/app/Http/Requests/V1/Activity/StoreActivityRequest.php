<?php

namespace Modules\Programs\Http\Requests\Api\V1\Activity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\V1\Activity\ActivityType;
use Modules\Programs\Rules\ProgramManagerOwnsProgram;

/**
 * Class StoreActivityRequest
 * * Validates the creation of a new program activity.
 * It enforces business rules such as verifying program ownership and valid activity types.
 */
class StoreActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Checks if the user has the 'activities.create' permission.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('activities.create');
    }

    /**
     * Get the validation rules that apply to the request.
     * * - program_id: Must exist and belong to the authenticated program manager.
     * - activity_type: Must be a valid value from the ActivityType Enum.
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
