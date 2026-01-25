<?php

namespace Modules\CaseManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\SessionType;

/**
 * Class UpdateCaseSessionRequest
 *
 * Handles validation for updating an existing case session.
 * Allows partial updates, validating only the fields present in the request.
 * Includes business rules regarding which fields can/cannot be changed.
 */
class UpdateCaseSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     *
     * @todo Implement proper authorization, e.g., check if user can update the session
     */
    public function authorize(): bool
    {
        return true;
        // Example for future:
        // return $this->user()->can('update', $this->route('caseSession'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * All fields are optional (partial update), but if present, they must be valid.
     *
     * @return array<string, array|mixed>
     */
    public function rules(): array
    {
        return [
            /**
             * Type of the session.
             * Optional, but if present must be a valid SessionType enum value.
             */
            'session_type' => [
                'sometimes',
                'required',
                Rule::in(SessionType::values()),
            ],

            /**
             * Date of the session.
             * Optional, but if present must be a valid date.
             */
            'session_date' => [
                'sometimes',
                'required',
                'date',
            ],

            /**
             * Duration of the session in minutes.
             * Optional, nullable, integer, minimum 1.
             */
            'duration_minutes' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            /**
             * Optional notes about the session.
             */
            'notes' => [
                'sometimes',
                'nullable',
                'string',
            ],

            /**
             * Optional recommendations provided during the session.
             */
            'recommendations' => [
                'sometimes',
                'nullable',
                'string',
            ],

            /**
             * Business rule:
             * Usually, we do NOT allow changing the specialist after session creation.
             *
             * If allowed in the future, you can uncomment and use:
             *
             * 'conducted_by' => [
             *     'sometimes',
             *     'required',
             *     'exists:specialists,id',
             * ],
             */
        ];
    }
}
