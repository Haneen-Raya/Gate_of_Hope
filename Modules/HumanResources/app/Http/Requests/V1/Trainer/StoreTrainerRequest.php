<?php

namespace Modules\HumanResources\Http\Requests\V1\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\V1\CertificationLevel;
use Modules\HumanResources\Enums\V1\Gender;

/**
 * Class StoreTrainerRequest
 * * Validates the profile and professional credentials for a new Trainer.
 * This request manages the delicate balance between personal data, professional
 * classification, and financial parameters like hourly rates.
 * * @package Modules\HumanResources\Http\Requests\V1\Trainer
 */
class StoreTrainerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to register new trainers.
     * * @note Authorization is typically handled via 'hr.trainers.create'
     * permission in the controller or middleware.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for trainer onboarding.
     * * Business Logic Constraints:
     * - profession_id: Links the trainer to a specific domain of expertise.
     * - gender/certification_level: Enforced via strictly typed PHP Enums.
     * - hourly_rate: Must be non-negative to ensure financial record integrity.
     * - status: Marked as 'prohibited' during creation to ensure trainers start
     * at a default state defined by the system, preventing manual status injection.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profession_id'       => ['required', 'exists:professions,id'],
            'gender'              => ['required', new Enum(Gender::class)],
            'date_of_birth'       => ['required', 'date'],
            'bio'                 => ['nullable', 'string'],
            'certification_level' => ['required', new Enum(CertificationLevel::class)],
            'hourly_rate'         => ['required', 'numeric', 'min:0'],
            'is_external'         => ['required', 'boolean'],

            /**
             * Status is prohibited on store to enforce system-controlled
             * state transitions. Use specialized activation endpoints instead.
             */
            'status'              => ['prohibited'],
        ];
    }
}
