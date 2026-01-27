<?php

namespace Modules\HumanResources\Http\Requests\V1\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\CertificationLevel;
use Modules\HumanResources\Enums\Gender;

class StoreTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // لاحقًا policy
    }

    public function rules(): array
    {
        return [
            'user_id'              => 'required|exists:users,id',
            'profession_id'        => 'required|exists:professions,id',
            'gender' => ['required', new Enum(Gender::class)],
            'date_of_birth'        => 'required|date',
            'bio'                  => 'nullable|string',
            'certification_level' => ['required', new Enum(CertificationLevel::class)],
            'hourly_rate'          => 'required|numeric|min:0',
            'is_external'          => 'required|boolean',
        ];
    }
}
