<?php

namespace Modules\HumanResources\Http\Requests\V1\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\CertificationLevel;
use Modules\HumanResources\Enums\Gender;
use Modules\HumanResources\Enums\TrainerStatus;

/**
 * Class UpdateTrainerRequest
 * * Handles the validation logic for modifying existing Trainer profiles.
 * This request supports PATCH-style updates, allowing individual fields to be
 * updated without requiring the entire resource representation.
 * * @package Modules\HumanResources\Http\Requests\V1\Trainer
 */
class UpdateTrainerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to update trainer records.
     * * @note Permission checks are usually delegated to 'hr.trainers.update'
     * within the Controller or via Policy.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for updating trainer data.
     * * Logical Enhancements:
     * - 'sometimes': Applied to all fields to facilitate partial updates.
     * - 'status': Unlike the Store request, status changes are permitted here
     * to manage the trainer's lifecycle (e.g., Active to On-Hold).
     * - Enums: Ensures that even during updates, data remains strictly typed.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profession_id'       => ['sometimes', 'exists:professions,id'],
            'gender'              => ['sometimes', new Enum(Gender::class)],
            'date_of_birth'       => ['sometimes', 'date'],
            'bio'                 => ['sometimes', 'nullable', 'string'],
            'certification_level' => ['sometimes', new Enum(CertificationLevel::class)],
            'hourly_rate'         => ['sometimes', 'numeric', 'min:0'],
            'is_external'         => ['sometimes', 'boolean'],

            /**
             * Status transitions are validated against the TrainerStatus Enum.
             */
            'status'              => ['sometimes', new Enum(TrainerStatus::class)],
        ];
    }
}
