<?php

namespace Modules\CaseManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\SessionType;

/**
 * Class StoreCaseSessionRequest
 *
 * Handles validation logic for creating a new case session.
 * Ensures that all required session data is valid before
 * being passed to the controller or service layer.
 */
class StoreCaseSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array|mixed>
     */
    public function rules(): array
    {
        return [
            /**
             * Type of the session (must be a valid SessionType enum value)
             */
            'session_type' => [
                'required',
                Rule::in(SessionType::values()),
            ],

            /**
             * Date when the session was conducted
             */
            'session_date' => ['required', 'date'],

            /**
             * Duration of the session in minutes
             */
            'duration_minutes' => ['required', 'integer', 'min:1'],

            /**
             * Optional notes related to the session
             */
            'notes' => ['nullable', 'string'],

            /**
             * Optional recommendations provided during the session
             */
            'recommendations' => ['nullable', 'string'],

            /**
             * Specialist who conducted the session
             * Must exist in specialists table
             */
            'conducted_by' => ['required', 'exists:specialists,id'],
        ];
    }
}
