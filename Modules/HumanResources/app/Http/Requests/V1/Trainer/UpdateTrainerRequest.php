<?php

namespace Modules\HumanResources\Http\Requests\V1\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\CertificationLevel;
use Modules\HumanResources\Enums\Gender;

class UpdateTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profession_id'        => 'sometimes|exists:professions,id',
            'gender' => ['required' , 'sometimes' , new Enum(Gender::class)],
            'date_of_birth'        => 'sometimes|date',
            'bio'                  => 'sometimes|nullable|string',
            'certification_level' => ['required', new Enum(CertificationLevel::class)],
            'hourly_rate'          => 'sometimes|numeric|min:0',
            'is_external'          => 'sometimes|boolean',
        ];
    }
}
