<?php

namespace Modules\HumanResources\Http\Requests\V1\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\V1\CertificationLevel;
use Modules\HumanResources\Enums\V1\Gender;

class StoreTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // لاحقًا policy
    }

    public function rules(): array
    {
        return [
            'profession_id'        => 'required|exists:professions,id',
            'gender' => ['required', new Enum(Gender::class)],
            'date_of_birth'        => 'required|date',
            'bio'                  => 'nullable|string',
            'certification_level' => ['required', new Enum(CertificationLevel::class)],
            'hourly_rate'          => 'required|numeric|min:0',
            'is_external'          => 'required|boolean',
            'status' => 'prohibited',
        ];
    }
}
