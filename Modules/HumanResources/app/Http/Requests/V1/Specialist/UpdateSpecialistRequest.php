<?php

namespace Modules\HumanResources\Http\Requests\V1\Specialist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\Gender;

class UpdateSpecialistRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gender' => ['required', new Enum(Gender::class)],
            'date_of_birth' => 'sometimes|date',
            'user_id' => 'sometimes|exists:users,id',
            'issue_category_id' => 'sometimes|exists:issue_categories,id',
        ];  
    }
}
